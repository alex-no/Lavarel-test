<?php

namespace App\Models;

use App\Models\Base\AdvModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends AdvModel
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_id',
        'amount',
        'currency',
        'payment_status',
        'description',
        'paid_at',
    ];
}
