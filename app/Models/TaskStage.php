<?php

namespace App\Models;

use App\Traits\SpaceableTrait;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskStage extends Model
{
    use HasFactory;
    use CrudTrait;
    use SpaceableTrait;

    protected $fillable = [
        'name',
        'deadline',
        'deadline_format_id'
    ];

    protected $appends = [ 'calculated_deadline' ];

    public function buttons()
    {
        return $this->belongsToMany(TaskButton::class, 'task_button_stages');
    }

    public function deadline_format(): BelongsTo
    {
        return $this->belongsTo(DeadlineFormat::class);
    }

    public function getCalculatedDeadlineAttribute()
    {
        return $this->deadline_format->value ?? 0 * $this->deadline;
    }

    protected static function booted()
    {
        static::created(function (self $stage) {
            $button = TaskButton::query()->updateOrCreate(
                [
                    'name' => 'Комментировать',
                    'color' => 'green',
                    'icon' => 'comment',
                    'is_default' => true,
                ],
                [
                    'name' => 'Комментировать',
                    'color' => 'green',
                    'icon' => 'comment',
                    'is_default' => true,
                ]
            );

            $button->visible()->attach($stage->id);

            $button->action()->update([
                'comment' => true
            ]);
        });
    }
}
