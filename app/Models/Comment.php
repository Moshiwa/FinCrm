<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

class Comment extends Model
{
    protected $fillable = [
        'deal_id',
        'type',
        'title',
        'content',
        'author_id',
        'temp_id'
    ];

    protected $appends = [
        'date_create'
    ];

    public function files(): BelongsToMany
    {
        return $this->belongsToMany(
            File::class,
            'comments_files',
            'comment_id',
            'file_id'
        );
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getDateCreateAttribute() {
        return Carbon::make($this->created_at)->translatedFormat('j F Y H:i');
    }

    public static function booted()
    {
        parent::boot();
        static::deleting(function (self $comment) {
            $comment->files->each->delete();
        });
    }
}
