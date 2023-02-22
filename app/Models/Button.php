<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
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
        'options'
    ];

    public $casts = [
        'options' => 'array'
    ];

    public function visible()
    {
        return $this->belongsToMany(Stage::class, 'button_stages');
    }

    public function action()
    {
        return $this->HasOne(ButtonAction::class);
    }

    protected static function booted()
    {
        static::created(function (self $button) {
            $button->action()->create();
        });
    }
}
