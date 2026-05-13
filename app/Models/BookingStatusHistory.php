<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingStatusHistory extends Model
{
    protected $table = 'booking_status_histories';

    protected $fillable = [
        'booking_id',
        'old_status',
        'new_status',
        'note',
        'changed_by',
    ];

    protected $casts = [
        'booking_id' => 'integer',
        'changed_by' => 'integer',
    ];
}