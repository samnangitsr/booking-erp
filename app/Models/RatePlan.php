<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RatePlan extends Model
{
    use SoftDeletes;

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

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }
}
