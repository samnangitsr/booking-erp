<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Refund extends Model
{
    use SoftDeletes;

    protected $table = 'refunds';

    protected $fillable = [
        'booking_id',
        'payment_id',
        'refund_no',
        'refund_date',
        'refund_amount',
        'refund_method',
        'reason',
        'status',
        'approved_by',
    ];

    protected $casts = [
        'booking_id' => 'integer',
        'payment_id' => 'integer',
        'refund_date' => 'datetime',
        'refund_amount' => 'decimal:2',
        'approved_by' => 'integer',
    ];
}