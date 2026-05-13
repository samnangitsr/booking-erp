<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use SoftDeletes;

    protected $table = 'coupons';

    protected $fillable = [
        'code',
        'name',
        'discount_type',
        'discount_value',
        'start_date',
        'end_date',
        'usage_limit',
        'usage_per_customer',
        'used_count',
        'status',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'usage_limit' => 'integer',
        'usage_per_customer' => 'integer',
        'used_count' => 'integer',
    ];
}