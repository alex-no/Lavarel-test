<?php
namespace App\Services\Payment\Drivers;

use App\Services\Payment\PaymentInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Illuminate\Support\Facades\Log;
use App\Models\Order;

class LiqPayDriver implements PaymentInterface
{
    public const NAME = 'LiqPay';
    public const VERSION = '1.0.1';
    public const PAYMENT_URL = 'https://www.liqpay.ua/api/3/checkout';
    // public const PAYMENT_CALLBACK_URL = 'https://www.liqpay.ua/api/3/callback';

    /**
     * LiqPayDriver constructor.
     * @param string $publicKey - The public key for LiqPay API.
     * @param string $privateKey - The private key for LiqPay API.
     * @param string $callbackUrl - The URL to which the payment response will be sent.
     */
    public function __construct(
        private string $publicKey,
        private string $privateKey,
        private string $callbackUrl,
        private string $resultUrl
    ) {}

    /**
     * Creates a LiqPay payment form data.
     * Returns a URL or HTML form that can be used to initiate payment.
     *
     * @param array $params Payment parameters: amount, currency, description, order_id, etc.
     * @return array{
     *     action: string,         // Form action URL
     *     method: 'POST'|'GET',   // Form method
     *     data: array<string, string> // Key-value pairs for form inputs
     * }
     */
    public function createPayment(array $params): array
    {
        $data = [
            'version'     => '3',
            'public_key'  => $this->publicKey,
            'action'      => 'pay',
            'amount'      => $params['amount'],
            'currency'    => $params['currency'] ?? 'UAH',
            'description' => $params['description'],
            'order_id'    => $params['order_id'],
            'server_url'  => $this->callbackUrl,
            'result_url'  => $this->resultUrl ?? null,
        ];

        $json = base64_encode(json_encode($data));
        $signature = $this->generateSignature($json);

        return [
            'action'    => self::PAYMENT_URL,
            'method' => 'POST',
            'data'   => [
                'data'      => $json,
                'signature' => $signature,
            ],
        ];
    }

    /**
     * Handles the payment callback from the payment gateway.
     * This method should process the callback data,
     * verify the payment, and return the result.
     * @param array $post
     * @return Order|null
     */
    public function handleCallback(array $post): ?Order
    {
        if (empty($post['data']) || empty($post['signature'])) {
            throw new BadRequestHttpException("Missing data or signature.");
        }
        if (!$this->verifySignature($post['data'], $post['signature'])) {
            throw new BadRequestHttpException("Invalid signature.");
        }

        $data = json_decode(base64_decode($post['data']), true);

        $orderId = $data['order_id'] ?? null;
        $status = $data['status'] ?? null;

        if (!$orderId || !$status) {
            throw new BadRequestHttpException("Invalid callback data.");
        }

        $order = Order::where('order_id', $orderId)->first();
        if (!$order) {
            return null; // Order not found
        }
        $order->payment_status = $status;

        return $order;
    }

    /**
     * Verifies the signature of the payment data.
     * This method should check if the signature matches the expected value.
     * @param string $json
     * @param string $signature
     * @return bool
     */
    public function verifySignature(string $json, string $signature): bool
    {
        return hash_equals($this->generateSignature($json), $signature);
    }

    /**
     * Generates LiqPay signature.
     *
     * @param string $json
     * @return string
     */
    protected function generateSignature(string $json): string
    {
        return base64_encode(sha1($this->privateKey . $json . $this->privateKey, true));
    }
}
