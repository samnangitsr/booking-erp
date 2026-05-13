<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyImage extends Model
{
    protected $table = 'property_images';

    protected $fillable = [
        'property_id',
        'image_path',
        'title',
        'sort_order',
        'is_cover',
        'status',
    ];

    protected $casts = [
        'property_id' => 'integer',
        'sort_order' => 'integer',
        'is_cover' => 'boolean',
    ];
}