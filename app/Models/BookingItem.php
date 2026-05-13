<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BookingItem extends Model
{
    protected $table = 'booking_items';

    protected $fillable = [
        'booking_id',
        'property_id',
        'room_type_id',
        'rate_plan_id',
        'room_id',
        'room_name',
        'rate_plan_name',
        'check_in_date',
        'check_out_date',
        'nights',
        'rooms_count',
        'adults',
        'children',
        'unit_price',
        'total_price',
        'status',
    ];

    protected $casts = [
        'booking_id' => 'integer',
        'property_id' => 'integer',
        'room_type_id' => 'integer',
        'rate_plan_id' => 'integer',
        'room_id' => 'integer',
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'nights' => 'integer',
        'rooms_count' => 'integer',
        'adults' => 'integer',
        'children' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function ratePlan(): BelongsTo
    {
        return $this->belongsTo(RatePlan::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function dailyRates(): HasMany
    {
        return $this->hasMany(BookingItemDailyRate::class);
    }
}
