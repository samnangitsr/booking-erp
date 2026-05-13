<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
    use SoftDeletes;

    public const APPROVAL_STATUSES = ['pending', 'approved', 'rejected'];

    public const STATUSES = ['active', 'inactive', 'suspended'];

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
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'check_in_time' => 'string',
        'check_out_time' => 'string',
        'min_check_in_age' => 'integer',
        'is_featured' => 'boolean',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    public function propertyType(): BelongsTo
    {
        return $this->belongsTo(PropertyType::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function roomTypes(): HasMany
    {
        return $this->hasMany(RoomType::class);
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(PropertyImage::class)->orderBy('sort_order');
    }

    public function coverImage(): ?PropertyImage
    {
        return $this->images()->orderByDesc('is_cover')->orderBy('sort_order')->first();
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'property_amenity')->withTimestamps();
    }

    public function policies(): HasMany
    {
        return $this->hasMany(PropertyPolicy::class)->orderBy('policy_type');
    }

    public function nearbyPlaces(): HasMany
    {
        return $this->hasMany(NearbyPlace::class)->orderBy('distance_km');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(PropertyContact::class)->orderByDesc('is_primary');
    }
}
