<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Commission extends Model
{
    use SoftDeletes;

    protected $table = 'commissions';

    protected $fillable = [
        'booking_id',
        'property_id',
        'partner_id',
        'commission_rate',
        'booking_amount',
        'commission_amount',
        'partner_amount',
        'status',
    ];

    protected $casts = [
        'booking_id' => 'integer',
        'property_id' => 'integer',
        'partner_id' => 'integer',
        'commission_rate' => 'decimal:2',
        'booking_amount' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'partner_amount' => 'decimal:2',
    ];
}