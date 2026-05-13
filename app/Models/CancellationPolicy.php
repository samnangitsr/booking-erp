<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CancellationPolicy extends Model
{
    use SoftDeletes;

    protected $table = 'cancellation_policies';

    protected $fillable = [
        'property_id',
        'name',
        'description',
        'free_cancel_before_days',
        'penalty_type',
        'penalty_value',
        'status',
    ];

    protected $casts = [
        'property_id' => 'integer',
        'free_cancel_before_days' => 'integer',
        'penalty_value' => 'decimal:2',
    ];
}