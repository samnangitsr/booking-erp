<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes;

    protected $table = 'payments';

    protected $fillable = [
        'booking_id',
        'customer_id',
        'payment_no',
        'payment_method_id',
        'payment_date',
        'amount',
        'currency_code',
        'transaction_id',
        'reference_no',
        'payment_gateway',
        'status',
        'note',
        'received_by',
    ];

    protected $casts = [
        'booking_id' => 'integer',
        'customer_id' => 'integer',
        'payment_method_id' => 'integer',
        'payment_date' => 'datetime',
        'amount' => 'decimal:2',
        'received_by' => 'integer',
    ];
}