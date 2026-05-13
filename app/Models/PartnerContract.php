<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PartnerContract extends Model
{
    use SoftDeletes;

    protected $table = 'partner_contracts';

    protected $fillable = [
        'partner_id',
        'contract_no',
        'start_date',
        'end_date',
        'commission_rate',
        'contract_file',
        'status',
    ];

    protected $casts = [
        'partner_id' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'commission_rate' => 'decimal:2',
    ];
}