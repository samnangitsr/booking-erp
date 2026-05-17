<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyRate extends Model
{
    protected $table = 'daily_rates';

    protected $fillable = [
        'property_id',
        'room_type_id',
        'rate_plan_id',
        'rate_date',
        'base_price',
        'adult_price',
        'child_price',
        'extra_bed_price',
        'currency_code',
        'min_stay',
        'max_stay',
        'closed_to_arrival',
        'closed_to_departure',
        'stop_sell',
    ];

    protected $casts = [
        'property_id' => 'integer',
        'room_type_id' => 'integer',
        'rate_plan_id' => 'integer',
        'rate_date' => 'date',
        'base_price' => 'decimal:2',
        'adult_price' => 'decimal:2',
        'child_price' => 'decimal:2',
        'extra_bed_price' => 'decimal:2',
        'min_stay' => 'integer',
        'max_stay' => 'integer',
        'closed_to_arrival' => 'boolean',
        'closed_to_departure' => 'boolean',
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

    public function ratePlan(): BelongsTo
    {
        return $this->belongsTo(RatePlan::class);
    }
}
