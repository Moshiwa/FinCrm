<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DealComment extends Model
{
    protected $fillable = [
        'deal_id',
        'type',
        'content',
        'author_id'
    ];

    public function files(): BelongsToMany
    {
        return $this->belongsToMany(
            File::class,
            'deal_comments_files',
            'deal_comment_id',
            'file_id'
        );
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
