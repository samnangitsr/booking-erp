<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    protected $table = 'wishlists';

    protected $fillable = [
        'customer_id',
        'property_id',
    ];

    protected $casts = [
        'customer_id' => 'integer',
        'property_id' => 'integer',
    ];
}