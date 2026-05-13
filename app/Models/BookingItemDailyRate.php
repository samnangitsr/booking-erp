<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingItemDailyRate extends Model
{
    protected $table = 'booking_item_daily_rates';

    protected $fillable = [
        'booking_item_id',
        'stay_date',
        'price',
        'tax_amount',
        'fee_amount',
        'total_amount',
    ];

    protected $casts = [
        'booking_item_id' => 'integer',
        'stay_date' => 'date',
        'price' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'fee_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];
}