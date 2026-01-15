<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_id',
        'customer_id',
        'vendor_id',
        'shipping_address',
        'payment_method',
        'phone_number',
        'total_amount',
        'status',
        'items',
    ];

    protected $casts = [
        'items' => 'array',
    ];
}
