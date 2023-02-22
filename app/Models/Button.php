<?php

namespace App\Models;

use App\Enums\ColorStyleEnum;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Backpack\CRUD\Tests81\Unit\Models\Enums\StyleEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Button extends Model
{
    use HasFactory;
    use CrudTrait;

    public $timestamps = false;

    public $fillable = [
        'name',
        'pipeline_id',
        'color',
        'icon',
        'is_default'
    ];

    protected $appends = [
        'color_style',
        'icon_style'
    ];

    public function visible()
    {
        return $this->belongsToMany(Stage::class, 'button_stages');
    }

    public function action()
    {
        return $this->HasOne(ButtonAction::class);
    }

    public function getColorStyleAttribute()
    {
        return "btn-custom__$this->color";
    }

    public function getIconStyleAttribute()
    {
        return match ($this->icon) {
            'comment' => 'las la-comment',
            default => 'las la-angle-double-right',
        };
    }

    protected static function booted()
    {
        static::created(function (self $button) {
            $button->action()->create();
        });
    }
}
