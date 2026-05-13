<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Promotion extends Model
{
    use SoftDeletes;

    protected $table = 'promotions';

    protected $fillable = [
        'property_id',
        'promotion_code',
        'name',
        'promotion_type',
        'discount_value',
        'start_date',
        'end_date',
        'min_nights',
        'min_amount',
        'usage_limit',
        'used_count',
        'status',
    ];

    protected $casts = [
        'property_id' => 'integer',
        'discount_value' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'min_nights' => 'integer',
        'min_amount' => 'decimal:2',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
    ];
}