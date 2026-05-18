<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class RatePlan extends Model
{
    use SoftDeletes;

    public const STATUSES = ['active', 'inactive'];

    public const MEAL_PLANS = ['none', 'breakfast', 'half_board', 'full_board'];

    public const PAYMENT_POLICIES = ['pay_now', 'pay_later', 'pay_at_property'];

    protected $table = 'rate_plans';

    protected $fillable = [
        'property_id',
        'room_type_id',
        'rate_plan_code',
        'name',
        'meal_plan',
        'cancellation_policy_id',
        'payment_policy',
        'is_refundable',
        'status',
    ];

    protected $casts = [
        'property_id' => 'integer',
        'room_type_id' => 'integer',
        'cancellation_policy_id' => 'integer',
        'is_refundable' => 'boolean',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function cancellationPolicy(): BelongsTo
    {
        return $this->belongsTo(CancellationPolicy::class);
    }

    public function dailyRates(): HasMany
    {
        return $this->hasMany(DailyRate::class);
    }
}
