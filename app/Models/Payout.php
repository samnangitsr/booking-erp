<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payout extends Model
{
    use SoftDeletes;

    protected $table = 'payouts';

    protected $fillable = [
        'partner_id',
        'payout_no',
        'payout_date',
        'total_booking_amount',
        'total_commission',
        'payout_amount',
        'payment_method_id',
        'status',
        'approved_by',
    ];

    protected $casts = [
        'partner_id' => 'integer',
        'payout_date' => 'date',
        'total_booking_amount' => 'decimal:2',
        'total_commission' => 'decimal:2',
        'payout_amount' => 'decimal:2',
        'payment_method_id' => 'integer',
        'approved_by' => 'integer',
    ];
}