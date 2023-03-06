<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskStage extends Model
{
    use HasFactory;
    use CrudTrait;

    protected $fillable = [
        'name',
        'color'
    ];

    public function buttons()
    {
        return $this->belongsToMany(TaskButton::class, 'task_button_stages');
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
