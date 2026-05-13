<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';

    protected $fillable = [
        'company_id',
        'key',
        'value',
        'type',
        'group',
        'is_public',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'is_public' => 'boolean',
    ];
}