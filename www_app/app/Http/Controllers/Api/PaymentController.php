<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;


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
     * Returns the list of available payment drivers and the default one.
     *
     * @OA\Get(
     *     path="/api/payments",
     *     summary="Get list of available payment drivers and default one",
     *     tags={"Payment"},
     *     @OA\Response(
     *         response=200,
     *         description="List of available drivers and default driver",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="drivers",
     *                 type="array",
     *                 @OA\Items(type="string", example="paypal"),
     *                 @OA\Items(type="string", example="liqpay")
     *             ),
     *             @OA\Property(
     *                 property="default",
     *                 type="string",
     *                 example="paypal"
     *             )
     *         )
     *     )
     * )
     *
     * @return array
     */
    public function drivers(): array
    {
        $drivers = config('payment.drivers', []);
        $default = config('payment.default');

        return [
            'drivers' => array_keys($drivers),
            'default' => $default,
        ];
    }

    /**
     * @OA\Post(
     *     path="/api/payments/create",
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
            'currency' => 'required|string',
            'pay_system' => 'required|string',
            'order_id' => 'nullable|string|max:64',
        ]);

        $user = Auth::user();
        $orderId = $request->input('order_id');
        $driverName = $request->input('pay_system');

        if (empty($orderId)) {
            $orderId = 'ORD-' . now()->format('Ymd-His') . '-' . Str::random(6);

            $order = Order::create([
                'user_id'    => $user->id,
                'order_id'   => $orderId,
                'amount'     => $request->amount,
                'pay_system' => $driverName
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
            $order->update(['currency' => $request->currency]);
            $order->update(['pay_system' => $driverName]);
            $order->update(['description' => 'Payment for Order #' . $orderId]);
        }

        // Creating a payment via PaymentManager
        $paymentData = app('payment')->getDriver($driverName)->createPayment([
            'order_id'    => $orderId,
            'amount'      => $order->amount,
            'currency'    => $order->currency,
            'description' => $order->description,
        ]);

        return response()->json([
            'success' => true,
            'payment' => $paymentData,
            'orderId' => $orderId,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/payments/handle{driverName}",
     *     summary="API Payments Handle",
     *     description="Returns information about Handle Payments.",
     *     tags={"Payment"},
     *     @OA\Parameter(
     *         name="driverName",
     *         in="path",
     *         description="Payment driver name",
     *         required=true,
     *         @OA\Schema(type="string", example="paypal")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Raw POST data, varies depending on payment driver",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 additionalProperties=true
     *             )
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
    public function handle(string $driverName): \Illuminate\Http\JsonResponse
    {
        $post = request()->post();

        if (empty($post)) {
            throw new BadRequestHttpException("Missing POST-data.");
        }

        $driver = app('payment')->getDriver($driverName);

        $order = $driver->handleCallback($post);
        if (!$order) {
            throw new ModelNotFoundException("Order not found.");
        }

        $order->paid_at = $order->payment_status === 'success' ? now() : null;
        $order->save();
        Log::info("Payment callback received for order #$order->order_id with status: $order->status");

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
        if (!$order) {
            throw ValidationException::withMessages(['order_id' => 'Order not found.']);
        }

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
