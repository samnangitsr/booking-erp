<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChildAgePolicy extends Model
{
    protected $table = 'child_age_policies';

    protected $fillable = [
        'property_id',
        'min_age',
        'max_age',
        'charge_type',
        'charge_value',
        'note',
    ];

    protected $casts = [
        'property_id' => 'integer',
        'min_age' => 'integer',
        'max_age' => 'integer',
        'charge_value' => 'decimal:2',
    ];
}