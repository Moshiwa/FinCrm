<?php

namespace App\Models;

use App\Enums\CommentTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DealComment extends Model
{
    protected $fillable = [
        'deal_id',
        'type',
        'title',
        'content',
        'author_id'
    ];

    protected $casts = [
        'type' => CommentTypeEnum::class,
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
