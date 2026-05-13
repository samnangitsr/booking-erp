<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use SoftDeletes;

    protected $table = 'bookings';

    protected $fillable = [
        'company_id',
        'branch_id',
        'customer_id',
        'property_id',
        'booking_no',
        'booking_date',
        'check_in_date',
        'check_out_date',
        'nights',
        'total_rooms',
        'total_adults',
        'total_children',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'fee_amount',
        'grand_total',
        'paid_amount',
        'due_amount',
        'currency_code',
        'booking_source',
        'payment_status',
        'booking_status',
        'special_request',
        'cancelled_at',
        'cancellation_reason',
        'created_by',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'branch_id' => 'integer',
        'customer_id' => 'integer',
        'property_id' => 'integer',
        'booking_date' => 'datetime',
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'nights' => 'integer',
        'total_rooms' => 'integer',
        'total_adults' => 'integer',
        'total_children' => 'integer',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'fee_amount' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
        'cancelled_at' => 'datetime',
        'created_by' => 'integer',
    ];
}