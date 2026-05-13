<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';

    protected $fillable = [
        'company_id',
        'user_id',
        'action',
        'module',
        'description',
        'subject_type',
        'subject_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'user_id' => 'integer',
        'subject_id' => 'integer',
        'old_values' => 'array',
        'new_values' => 'array',
    ];
}