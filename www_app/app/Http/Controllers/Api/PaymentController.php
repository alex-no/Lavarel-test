<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

/**
 * PaymentController class for working with Payment models.
 *
 * @OA\Tag(
 *     name="Payment",
 *     description="API for working with payment models, including creating payments, handling callbacks, and verifying signatures."
 * )
 */
class PaymentController extends Controller

{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->only(['create', 'result']);
    }

    /**
     * @OA\Post(
     *     path="/api/payments",
     *     security={{"bearerAuth":{}}},
     *     summary="API Create New Payment",
     *     description="Returns information about New Payment.",
     *     tags={"Payment"},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"amount", "pay_system"},
     *             @OA\Property(property="amount", type="string", example="100.00"),
     *             @OA\Property(property="pay_system", type="string", example="lyqpay"),
     *             @OA\Property(property="order_id", type="string", example="ORD-20250529-045325-abcd1234")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Created new payment",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="payment", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error"
     *     )
     * )
     */
    public function create(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric',
            'pay_system' => 'required|string',
            'order_id' => 'nullable|string|max:64',
        ]);

        $user = Auth::user();
        $orderId = $request->input('order_id');

        if (empty($orderId)) {
            $orderId = 'ORD-' . now()->format('Ymd-His') . '-' . Str::random(6);

            $order = Order::create([
                'user_id'     => $user->id,
                'order_id'    => $orderId,
                'amount'      => $request->amount,
                'currency'    => 'UAH',
                'description' => 'Payment for Order #' . $orderId,
            ]);
        } else {
            $order = Order::where('order_id', $orderId)->first();

            if (!$order) {
                throw ValidationException::withMessages(['order_id' => 'Order not found.']);
            }

            if ($order->payment_status !== 'pending') {
                throw ValidationException::withMessages(['order_id' => 'Order is not in a valid state for payment.']);
            }

            $order->update(['amount' => $request->amount]);
        }

        $paymentData = app('payment')->getDriver()->createPayment([
            'order_id'    => $orderId,
            'amount'      => $order->amount,
            'currency'    => $order->currency,
            'description' => $order->description,
            'result_url'  => config('app.url') . '/api/payments/success',
        ]);

        return response()->json([
            'success' => true,
            'payment' => $paymentData,
            'orderId' => $orderId,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/payments/handle",
     *     summary="API Payments Handle",
     *     description="Returns information about Handle Payments.",
     *     tags={"Payment"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object", example="{data: 'example_data'}"),
     *             @OA\Property(property="signature", type="string", example="c2lnbmF0dXJlX2V4YW1wbGU=")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Updated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error"
     *     )
     * )
     */
    public function handle(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'data' => 'required|array',
            'signature' => 'required|string',
        ]);

        $driver = app('payment')->getDriver();

        if (!$driver->verifySignature($request->input('data'), $request->input('signature'))) {
            throw ValidationException::withMessages(['signature' => 'Invalid signature.']);
        }

        $data = $driver->handleCallback($request->all());

        $orderId = $data['order_id'] ?? null;
        $status = $data['status'] ?? null;

        if (!$orderId || !$status) {
            throw ValidationException::withMessages(['data' => 'Invalid callback data.']);
        }

        $order = Order::where('order_id', $orderId)->firstOrFail();
        $order->update([
            'payment_status' => $status,
            'paid_at' => $status === 'success' ? now() : null,
        ]);

        Log::info("Payment callback received for order #$orderId with status: $status");

        return response()->json(['success' => true]);
    }

    /**
     * @OA\Get(
     *     path="/api/payments/result",
     *     security={{"bearerAuth":{}}},
     *     summary="API Payments Result",
     *     description="Returns information about Payment Result.",
     *     tags={"Payment"},
     *     @OA\Parameter(
     *         name="orderId",
     *         in="query",
     *         required=true,
     *         description="Order ID to get payment result for",
     *         @OA\Schema(type="string", example="ORD-20250529-045325-abcd1234")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Processed payment result",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="order", type="object", example="{order_id: 123456, amount: 100.00, currency: USD}")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error"
     *     )
     * )
     */
    public function result(Request $request): \Illuminate\Http\JsonResponse
    {
        $orderId = $request->query('orderId');

        $order = Order::where('order_id', $orderId)->firstOrFail();

        return response()->json([
            'success' => $order->payment_status === 'success',
            'order' => [
                'order_id' => $order->order_id,
                'amount'   => $order->amount,
                'currency' => $order->currency,
                'status'   => $order->payment_status,
                'paid_at'  => $order->paid_at,
            ],
        ]);
    }
}
