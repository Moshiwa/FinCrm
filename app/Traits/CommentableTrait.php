<?php

namespace App\Traits;

use App\Models\Comment;

trait CommentableTrait
{
    public function comments()
    {
        return static::morphMany(Comment::class, 'commentable');
    }
}
