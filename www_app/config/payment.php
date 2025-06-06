<?php

return [
    'driver' => env('PAYMENT_DRIVER', 'liqpay'),

    'drivers' => [
        'liqpay' => [
            'class' => \App\Services\Payment\Drivers\LiqPayDriver::class,
            'config' => [
                'publicKey'   => env('LIQPAY_PUBLIC_KEY'),
                'privateKey'  => env('LIQPAY_PRIVATE_KEY'),
                'callbackUrl' => env('LIQPAY_CALLBACK_URL'),
                'resultUrl'   => env('LIQPAY_RESULT_URL'),
            ],
        ],
    ],
];
