<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewReply extends Model
{
    protected $table = 'review_replies';

    protected $fillable = [
        'review_id',
        'replied_by',
        'message',
        'replied_at',
    ];

    protected $casts = [
        'review_id' => 'integer',
        'replied_by' => 'integer',
        'replied_at' => 'datetime',
    ];
}