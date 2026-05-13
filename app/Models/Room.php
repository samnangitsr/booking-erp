<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use SoftDeletes;

    public const STATUSES = ['available', 'occupied', 'maintenance', 'inactive'];

    protected $table = 'rooms';

    protected $fillable = [
        'property_id',
        'room_type_id',
        'room_number',
        'floor',
        'status',
    ];

    protected $casts = [
        'property_id' => 'integer',
        'room_type_id' => 'integer',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }
}
