<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyPolicy extends Model
{
    protected $table = 'property_policies';

    protected $fillable = [
        'property_id',
        'policy_type',
        'title',
        'description',
        'status',
    ];

    protected $casts = [
        'property_id' => 'integer',
    ];
}