<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric',
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

        // Подключение драйвера оплаты будет позже
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

    public function handle(Request $request)
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

    public function result(Request $request)
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
