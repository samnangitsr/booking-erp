<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $table = 'invoice_items';

    protected $fillable = [
        'invoice_id',
        'item_type',
        'description',
        'qty',
        'unit_price',
        'total_amount',
    ];

    protected $casts = [
        'invoice_id' => 'integer',
        'qty' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];
}