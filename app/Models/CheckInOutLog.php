<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CheckInOutLog extends Model
{
    protected $table = 'check_in_out_logs';

    protected $fillable = [
        'booking_id',
        'room_id',
        'guest_id',
        'check_in_at',
        'check_out_at',
        'key_card_no',
        'deposit_amount',
        'note',
        'handled_by',
    ];

    protected $casts = [
        'booking_id' => 'integer',
        'room_id' => 'integer',
        'guest_id' => 'integer',
        'check_in_at' => 'datetime',
        'check_out_at' => 'datetime',
        'deposit_amount' => 'decimal:2',
        'handled_by' => 'integer',
    ];
}