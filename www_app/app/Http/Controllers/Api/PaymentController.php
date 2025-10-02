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
use Symfony\Component\HttpKernel\Exception\HttpException;


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
     * Creates a payment for the given order and driver.
     *
     * @OA\Post(
     *     path="/api/payments/create",
     *     security={{"bearerAuth":{}}},
     *     summary="API Create New Payment",
     *     description="Returns information about New Payment.",
     *     tags={"Payment"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"amount", "driver", "orderId"},
     *             @OA\Property(property="amount", type="string", example="100.00"),
     *             @OA\Property(property="driver", type="string", example="stripe"),
     *             @OA\Property(property="orderId", type="string", example="ORD-12345")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="payment", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request"
     *     )
     * )
     *
     * @param Request $request
     * @return array<string,mixed>
     * @throws BadRequestHttpException
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
        }

        $order->update([
            'amount'      => $request->amount,
            'currency'    => $request->currency,
            'pay_system'  => $driverName,
            'description' => 'Payment for Order #' . $orderId,
        ]);
        if (!$order->save()) {
            throw new HttpException(500, "Failed to create order: " . implode(', ', $order->getFirstErrors()));
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
     * Handles the callback from payment providers.
     *
     * @OA\Post(
     *     path="/api/payments/handle/{driverName}",
     *     summary="Handle payment provider callback",
     *     description="Processes the callback sent by a payment provider (e.g., Stripe, PayPal, LiqPay).",
     *     tags={"Payment"},
     *     @OA\Parameter(
     *         name="driverName",
     *         in="path",
     *         description="Payment driver name",
     *         required=true,
     *         @OA\Schema(type="string", example="stripe")
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
     *         description="Callback processed successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request"
     *     )
     * )
     *
     * @param string $driverName
     * @return array<string,mixed>
     * @throws BadRequestHttpException
     * @throws ServerErrorHttpException
     */
    public function handle(string $driverName): array
    {
        if (empty($driverName)) {
            throw new BadRequestHttpException('Missing $driverName.');
        }

        $driver = app('payment')->getDriver($driverName);
        $data   = $driver->getCallbackData();

        $result = $driver->handleCallback($data);

        switch ($result['status']) {
            case 'processed':
                /** @var \app\models\Order $order */
                $order = $result['order'];
                $order->paid_at = $order->payment_status === 'success' ? date('Y-m-d H:i:s') : null;
                $order->save(false); // disable validation, can be replaced with a transaction

                Log::info("Payment callback processed for order #{$order->order_id} with status: {$order->payment_status}", __METHOD__);
                return ['success' => true];

            case 'ignored':
                Log::info("Payment callback ignored (event type not relevant)", __METHOD__);
                return ['success' => true, 'message' => 'Event ignored'];

            case 'not_found':
                Log::warning("Payment callback: order not found", __METHOD__);
                return ['success' => false, 'message' => 'Order not found'];

            default:
                Log::warning("Payment callback returned unexpected status: {$result['status']}", __METHOD__);
                return ['success' => false, 'message' => 'Unexpected status'];
        }
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
