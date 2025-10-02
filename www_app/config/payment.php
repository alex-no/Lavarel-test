<?php

return [
    'default' => 'paypal',

    'drivers' => [
        'paypal' => [
            'class' => \App\Services\Payment\Drivers\PayPalDriver::class,
            'config' => [
                'clientId'    => env('PAYPAL_CLIENT_ID'),
                'secret'      => env('PAYPAL_SECRET'),
                'callbackUrl' => env('CURRENT_URL') . '/api/payments/handle/paypal',
                'returnUrl'   => env('CURRENT_URL') . '/html/payment-success',
                'cancelUrl'   => env('CURRENT_URL') . '/html/payment-cancel',
            ],
        ],
        'liqpay' => [
            'class' => \App\Services\Payment\Drivers\LiqPayDriver::class,
            'config' => [
                'publicKey'   => env('LIQPAY_PUBLIC_KEY'),
                'privateKey'  => env('LIQPAY_PRIVATE_KEY'),
                'callbackUrl' => env('CURRENT_URL') . '/api/payments/handle/liqpay',
                'resultUrl'   => env('CURRENT_URL') . '/html/payment-result',
            ],
        ],
        'stripe' => [
            'class' => \App\Services\Payment\Drivers\StripeDriver::class,
            'config' => [
                'apiKey' => env('STRIPE_SECRET_KEY'), // Secret API key
                // 'publicKey' => env('STRIPE_PUBLIC_KEY'), // Public API key
                'webhookSecret' => env('STRIPE_WEBHOOK_SECRET'),  // Webhook signing secret
                'callbackUrl' => env('CURRENT_URL') . '/api/payments/handle/stripe',
                'returnUrl' => env('CURRENT_URL') . '/html/payment-success',
                'cancelUrl' => env('CURRENT_URL') . '/html/payment-cancel',
            ],
        ],
    ],
];
