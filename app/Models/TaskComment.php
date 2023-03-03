<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

class TaskComment extends Model
{

    const COMMENT = 'comment';
    const ACTION = 'action';
    const DOCUMENT = 'document';

    protected $fillable = [
        'deal_id',
        'type',
        'title',
        'content',
        'author_id'
    ];

    protected $appends = [
        'date_create'
    ];

    public function files(): BelongsToMany
    {
        return $this->belongsToMany(
            File::class,
            'task_comments_files',
            'task_comment_id',
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
}
