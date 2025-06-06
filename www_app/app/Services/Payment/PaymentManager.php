<?php

namespace App\Services\Payment;

use App\Services\Payment\PaymentInterface;
use Illuminate\Support\Facades\Config;
use InvalidArgumentException;

class PaymentManager
{
    private PaymentInterface $driver;

    public function __construct()
    {
        $this->init();
    }

    private function init(): void
    {
        $driverName = Config::get('payment.driver');
        $drivers = Config::get('payment.drivers');

        if (!$driverName || !isset($drivers[$driverName])) {
            throw new InvalidArgumentException("Payment driver '$driverName' not configured.");
        }

        $driverConfig = $drivers[$driverName];
        $driverClass = $driverConfig['class'];
        $config = $driverConfig['config'] ?? [];

        $this->driver = app()->makeWith($driverClass, $config);
    }

    public function getDriver(): PaymentInterface
    {
        return $this->driver;
    }
}
