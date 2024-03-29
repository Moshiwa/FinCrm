<?php

namespace App\Models;

use App\Enums\FieldsEntitiesEnum;
use App\Events\CreateTask;
use App\Traits\SpaceableTrait;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class Task extends Model
{
    use HasFactory;
    use CrudTrait;
    use SpaceableTrait;

    protected $fillable = [
        'name',
        'task_stage_id',
        'description',
        'deadline',
        'responsible_id',
        'manager_id',
        'executor_id',
    ];

    protected $appends = [
        'all_fields',
        'string_deadline',
    ];

    public $casts = [
        'deadline' => 'datetime:Y-m-d H:i:s'
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

    public function comments(): morphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function fields(): BelongsToMany
    {
        return $this->belongsToMany(Field::class, 'task_fields')
            ->where('entity', FieldsEntitiesEnum::task->value)
            ->withPivot('value');
    }

    public function getStringDeadlineAttribute() {
        return Carbon::make($this->deadline)->translatedFormat('j F Y H:i');
    }

    public function getAllFieldsAttribute(): Collection
    {
        $fields = $this->fields()->get();
        $all_fields = Field::includedTask()->get();

        foreach ($all_fields as $field) {
            foreach ($fields as $filled_field) {
                if ($filled_field->id === $field->id) {
                    continue(2);
                }
            }

            $fields->push($field);
        }

        return $fields;
    }

    public static function booted()
    {
        static::created(function (self $task) {
            event(new CreateTask($task));
        });
        static::deleting(function (self $task) {
            $task->comments->each->delete();
        });
    }


}
