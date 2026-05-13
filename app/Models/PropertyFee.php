<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyFee extends Model
{
    protected $table = 'property_fees';

    protected $fillable = [
        'property_id',
        'name',
        'fee_type',
        'fee_value',
        'applies_per',
        'is_mandatory',
        'status',
    ];

    protected $casts = [
        'property_id' => 'integer',
        'fee_value' => 'decimal:2',
        'is_mandatory' => 'boolean',
    ];
}