<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyContact extends Model
{
    protected $table = 'property_contacts';

    protected $fillable = [
        'property_id',
        'name',
        'position',
        'phone',
        'email',
        'is_primary',
    ];

    protected $casts = [
        'property_id' => 'integer',
        'is_primary' => 'boolean',
    ];
}