<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $table = 'customers';

    protected $fillable = [
        'user_id',
        'customer_code',
        'first_name',
        'last_name',
        'gender',
        'dob',
        'phone',
        'email',
        'nationality',
        'address',
        'status',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'dob' => 'date',
    ];
}