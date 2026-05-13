<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CouponUsage extends Model
{
    protected $table = 'coupon_usages';

    protected $fillable = [
        'coupon_id',
        'customer_id',
        'booking_id',
        'discount_amount',
        'used_at',
    ];

    protected $casts = [
        'coupon_id' => 'integer',
        'customer_id' => 'integer',
        'booking_id' => 'integer',
        'discount_amount' => 'decimal:2',
        'used_at' => 'datetime',
    ];
}