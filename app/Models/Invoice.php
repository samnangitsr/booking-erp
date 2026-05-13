<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes;

    protected $table = 'invoices';

    protected $fillable = [
        'booking_id',
        'customer_id',
        'invoice_no',
        'invoice_date',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'fee_amount',
        'grand_total',
        'paid_amount',
        'due_amount',
        'invoice_status',
        'payment_status',
    ];

    protected $casts = [
        'booking_id' => 'integer',
        'customer_id' => 'integer',
        'invoice_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'fee_amount' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
    ];
}