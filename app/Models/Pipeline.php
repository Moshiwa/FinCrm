<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pipeline extends Model
{
    use CrudTrait;
    use HasFactory;

    protected $guarded = ['id'];

    public function stages(): HasMany
    {
        return $this->hasMany(Stage::class);
    }

    protected static function booted()
    {
        static::created(function (self $pipeline) {
            $stages = [
                [
                    'name' => 'В работе',
                    'color' => '#0050FF'
                ],
                [
                    'name' => 'Выполнено',
                    'color' => '#28FC2A'
                ],
                [
                    'name' => 'Отменено',
                    'color' => '#FE3F6D'
                ],

            ];

            $pipeline->stages()->createMany($stages);
        });
    }

}
