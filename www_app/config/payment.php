<?php

return [
    'default' => 'paypal',

    'drivers' => [
        'paypal' => [
            'class' => \App\Services\Payment\Drivers\PayPalDriver::class,
            'config' => [
                'clientId'    => env('PAYPAL_CLIENT_ID'),
                'secret'      => env('PAYPAL_SECRET'),
                'callbackUrl' => env('CURRENT_URL') . '/api/payments/handle',
                'returnUrl'   => env('CURRENT_URL') . '/html/payment-success',
                'cancelUrl'   => env('CURRENT_URL') . '/html/payment-cancel',
            ],
        ],
        'liqpay' => [
            'class' => \App\Services\Payment\Drivers\LiqPayDriver::class,
            'config' => [
                'publicKey'   => env('LIQPAY_PUBLIC_KEY'),
                'privateKey'  => env('LIQPAY_PRIVATE_KEY'),
                'callbackUrl' => env('CURRENT_URL') . '/api/payments/handle',
                'resultUrl'   => env('CURRENT_URL') . '/html/payment-result',
            ],
        ],
    ],
];
