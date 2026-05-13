<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use SoftDeletes;

    protected $table = 'activities';

    protected $fillable = [
        'city_id',
        'partner_id',
        'activity_code',
        'name',
        'description',
        'duration_minutes',
        'base_price',
        'meeting_point',
        'status',
    ];

    protected $casts = [
        'city_id' => 'integer',
        'partner_id' => 'integer',
        'duration_minutes' => 'integer',
        'base_price' => 'decimal:2',
    ];
}