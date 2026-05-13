<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportExport extends Model
{
    protected $table = 'report_exports';

    protected $fillable = [
        'user_id',
        'report_type',
        'filters',
        'file_path',
        'export_type',
        'status',
    ];

    protected $casts = [
        'user_id' => 'integer',
    ];
}