<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentMethod extends Model
{
    use SoftDeletes;

    protected $table = 'payment_methods';

    protected $fillable = [
        'company_id',
        'name',
        'code',
        'account_name',
        'account_number',
        'status',
    ];

    protected $casts = [
        'company_id' => 'integer',
    ];
}