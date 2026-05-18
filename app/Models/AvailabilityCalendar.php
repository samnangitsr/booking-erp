<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AvailabilityCalendar extends Model
{
    protected $table = 'availability_calendars';

    protected $fillable = [
        'property_id',
        'room_type_id',
        'available_date',
        'total_rooms',
        'booked_rooms',
        'blocked_rooms',
        'available_rooms',
        'stop_sell',
    ];

    protected $casts = [
        'property_id' => 'integer',
        'room_type_id' => 'integer',
        'available_date' => 'date',
        'total_rooms' => 'integer',
        'booked_rooms' => 'integer',
        'blocked_rooms' => 'integer',
        'available_rooms' => 'integer',
        'stop_sell' => 'boolean',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    /**
     * Recalculate available_rooms so callers can simply set total/booked/blocked
     * and let the model derive the net availability.
     */
    public function recalcAvailable(): self
    {
        $this->available_rooms = max(0, $this->total_rooms - $this->booked_rooms - $this->blocked_rooms);

        return $this;
    }
}
