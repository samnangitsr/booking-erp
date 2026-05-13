<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayoutItem extends Model
{
    protected $table = 'payout_items';

    protected $fillable = [
        'payout_id',
        'commission_id',
        'booking_id',
        'amount',
    ];

    protected $casts = [
        'payout_id' => 'integer',
        'commission_id' => 'integer',
        'booking_id' => 'integer',
        'amount' => 'decimal:2',
    ];
}