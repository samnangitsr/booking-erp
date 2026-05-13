<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    protected $table = 'taxes';

    protected $fillable = [
        'country_id',
        'name',
        'tax_type',
        'tax_value',
        'is_inclusive',
        'status',
    ];

    protected $casts = [
        'country_id' => 'integer',
        'tax_value' => 'decimal:2',
        'is_inclusive' => 'boolean',
    ];
}