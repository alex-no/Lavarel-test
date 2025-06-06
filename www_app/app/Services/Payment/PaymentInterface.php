<?php

namespace App\Services\Payment;

interface PaymentInterface
{
    public function createPayment(array $params): array;

    public function handleCallback(array $request): ?array;

    public function verifySignature(string $data, string $signature): bool;
}
