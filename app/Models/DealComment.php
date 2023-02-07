<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DealComment extends Model
{
    protected $fillable = [
        'deal_id',
        'type',
        'content',
        'author_id'
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
