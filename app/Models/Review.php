<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use SoftDeletes;

    protected $table = 'reviews';

    protected $fillable = [
        'booking_id',
        'property_id',
        'customer_id',
        'rating',
        'title',
        'comment',
        'cleanliness_score',
        'location_score',
        'service_score',
        'value_score',
        'status',
    ];

    protected $casts = [
        'booking_id' => 'integer',
        'property_id' => 'integer',
        'customer_id' => 'integer',
        'rating' => 'decimal:2',
        'cleanliness_score' => 'decimal:2',
        'location_score' => 'decimal:2',
        'service_score' => 'decimal:2',
        'value_score' => 'decimal:2',
    ];
}