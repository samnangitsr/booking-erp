<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoomType extends Model
{
    use SoftDeletes;

    public const STATUSES = ['active', 'inactive'];

    protected $table = 'room_types';

    protected $fillable = [
        'property_id',
        'room_type_code',
        'name',
        'slug',
        'description',
        'max_adults',
        'max_children',
        'max_occupancy',
        'room_size',
        'room_size_unit',
        'total_rooms',
        'base_price',
        'status',
    ];

    protected $casts = [
        'property_id' => 'integer',
        'max_adults' => 'integer',
        'max_children' => 'integer',
        'max_occupancy' => 'integer',
        'room_size' => 'decimal:2',
        'total_rooms' => 'integer',
        'base_price' => 'decimal:2',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function ratePlans(): HasMany
    {
        return $this->hasMany(RatePlan::class);
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'room_type_amenity')->withTimestamps();
    }

    public function images(): HasMany
    {
        return $this->hasMany(RoomTypeImage::class)->orderBy('sort_order');
    }

    public function bedTypes(): BelongsToMany
    {
        return $this->belongsToMany(BedType::class, 'room_type_bed_type')
            ->withPivot('quantity')
            ->withTimestamps();
    }
}
