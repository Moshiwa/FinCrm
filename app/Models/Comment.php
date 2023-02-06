<?php

namespace App\Models;

use App\Traits\SpaceableTrait;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use SpaceableTrait;

    protected $fillable = [
        'type',
        'content',
        'author_id'
    ];

    public function commentable()
    {
        return $this->morphTo();
    }
}
