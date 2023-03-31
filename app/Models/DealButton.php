<?php

namespace App\Models;

use App\Traits\SpaceableTrait;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealButton extends Model
{
    use HasFactory;
    use CrudTrait;
    use SpaceableTrait;

    public $timestamps = false;

    public $fillable = [
        'name',
        'pipeline_id',
        'color',
        'icon',
        'is_default'
    ];

    public function visible()
    {
        return $this->belongsToMany(Stage::class, 'deal_button_stages');
    }

    public function action()
    {
        return $this->HasOne(DealButtonAction::class);
    }

    protected static function booted()
    {
        static::created(function (self $button) {
            $button->action()->create();
        });
    }
}
