<?php

namespace App\Traits;

use App\Models\DealComment;

trait CommentableTrait
{
    public function comments()
    {
        return static::morphMany(DealComment::class, 'commentable');
    }
}
