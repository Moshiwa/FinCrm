<?php

namespace App\Models;

use App\Traits\SpaceableTrait;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Backpack\CRUD\Tests81\Unit\Models\Enums\StyleEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskButton extends Model
{
    use HasFactory;
    use CrudTrait;
    use SpaceableTrait;

    public $timestamps = false;

    public $fillable = [
        'name',
        'color',
        'icon',
        'is_default'
    ];

    public function visible()
    {
        return $this->belongsToMany(TaskStage::class, 'task_button_stages');
    }

    public function action()
    {
        return $this->HasOne(TaskButtonAction::class);
    }

    protected static function booted()
    {
        static::created(function (self $button) {
            $button->action()->create();
        });
    }
}
