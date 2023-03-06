<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskStage extends Model
{
    use HasFactory;
    use CrudTrait;

    protected $fillable = [
        'name',
        'color'
    ];

    public function buttons(): HasMany
    {
        return $this->hasMany(TaskButton::class);
    }

    protected static function booted()
    {
        static::created(function (self $stage) {
            $button = $stage->buttons()->create([
                'name' => 'Комментировать',
                'color' => 'green',
                'icon' => 'comment',
                'is_default' => true,
            ]);

            $button->action()->update([
                'comment' => true
            ]);
        });
    }
}
