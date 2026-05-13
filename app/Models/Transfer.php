<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transfer extends Model
{
    use SoftDeletes;

    protected $table = 'transfers';

    protected $fillable = [
        'city_id',
        'partner_id',
        'transfer_code',
        'vehicle_type',
        'pickup_location',
        'dropoff_location',
        'capacity',
        'base_price',
        'status',
    ];

    protected $casts = [
        'city_id' => 'integer',
        'partner_id' => 'integer',
        'capacity' => 'integer',
        'base_price' => 'decimal:2',
    ];
}