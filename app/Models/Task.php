<?php

namespace App\Models;

use App\Events\CreateTask;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Task extends Model
{
    use HasFactory;
    use CrudTrait;

    protected $fillable = [
        'name',
        'task_stage_id',
        'description',
        'start',
        'end',
        'responsible_id',
        'manager_id',
        'executor_id',
    ];

    public $casts = [
        'start' => 'datetime:Y-m-d H:i:s',
        'end' => 'datetime:Y-m-d H:i:s',
    ];

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function executor(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(TaskStage::class, 'task_stage_id');
    }

    public function fields(): BelongsToMany
    {
        return $this->belongsToMany(Field::class, 'task_fields')
            ->where('entity', 'task')
            ->withPivot('value');
    }

    public function comments(): morphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    protected static function booted()
    {
        static::created(function (self $task) {
            event(new CreateTask($task));
        });
    }


}
