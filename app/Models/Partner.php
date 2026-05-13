<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Partner extends Model
{
    use SoftDeletes;

    protected $table = 'partners';

    protected $fillable = [
        'user_id',
        'partner_code',
        'business_name',
        'contact_name',
        'phone',
        'email',
        'address',
        'commission_rate',
        'payment_term_days',
        'status',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'commission_rate' => 'decimal:2',
        'payment_term_days' => 'integer',
    ];
}