<?php
namespace App\Services\Payment;

use App\Services\Payment\PaymentInterface;
use InvalidArgumentException;

class PaymentManager
{
    /**
     * Returns the payment driver instance.
     * This method provides access to the payment driver that has been initialized.
     * @param string $driverName
     * @return PaymentInterface The payment driver instance.
     *
     * @throws InvalidArgumentException|BindingResolutionException
     */
    public function getDriver($driverName): PaymentInterface
    {
        $drivers = config('payment.drivers', []);

        if (!$driverName || !isset($drivers[$driverName])) {
            throw new InvalidArgumentException("Payment driver '$driverName' not configured.");
        }

        $driverClass = $drivers[$driverName]['class'];
        $driverConfig = $drivers[$driverName]['config'] ?? [];

        return app()->make($driverClass, $driverConfig);
    }
}
