<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceBooking extends Model
{
    use SoftDeletes;

    protected $table = 'service_bookings';

    protected $fillable = [
        'customer_id',
        'service_type',
        'service_id',
        'booking_no',
        'booking_date',
        'service_date',
        'qty',
        'total_amount',
        'payment_status',
        'booking_status',
    ];

    protected $casts = [
        'customer_id' => 'integer',
        'service_id' => 'integer',
        'booking_date' => 'datetime',
        'service_date' => 'date',
        'qty' => 'integer',
        'total_amount' => 'decimal:2',
    ];
}