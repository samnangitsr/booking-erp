<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
    use SoftDeletes;

    protected $table = 'properties';

    protected $fillable = [
        'company_id',
        'partner_id',
        'property_type_id',
        'country_id',
        'city_id',
        'area_id',
        'property_code',
        'name',
        'slug',
        'star_rating',
        'description',
        'address',
        'latitude',
        'longitude',
        'phone',
        'email',
        'check_in_time',
        'check_out_time',
        'min_check_in_age',
        'is_featured',
        'approval_status',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'partner_id' => 'integer',
        'property_type_id' => 'integer',
        'country_id' => 'integer',
        'city_id' => 'integer',
        'area_id' => 'integer',
        'star_rating' => 'decimal:2',
        'latitude' => 'decimal:2',
        'longitude' => 'decimal:2',
        'check_in_time' => 'string',
        'check_out_time' => 'string',
        'min_check_in_age' => 'integer',
        'is_featured' => 'boolean',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];
}