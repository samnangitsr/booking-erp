<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class Notification extends Model
{
    protected $table = 'notifications';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'type',
        'notifiable_type',
        'notifiable_id',
        'data',
        'read_at',
    ];

    protected $casts = [
        'notifiable_id' => 'integer',
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $notification): void {
            if (empty($notification->id)) {
                $notification->id = (string) Str::uuid();
            }
        });
    }

    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }
}
