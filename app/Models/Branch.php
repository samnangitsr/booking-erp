<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use SoftDeletes;

    protected $table = 'branches';

    protected $fillable = [
        'company_id',
        'branch_code',
        'name',
        'phone',
        'email',
        'address',
        'manager_id',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'manager_id' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];
}