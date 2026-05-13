<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerDocument extends Model
{
    protected $table = 'customer_documents';

    protected $fillable = [
        'customer_id',
        'document_type',
        'document_no',
        'issue_country',
        'expiry_date',
        'file_path',
        'status',
    ];

    protected $casts = [
        'customer_id' => 'integer',
        'expiry_date' => 'date',
    ];
}