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
            $status_process = Status::query()->select('id')->where('name', 'process')->first();
            $status_done = Status::query()->select('id')->where('name', 'done')->first();
            $status_cancel = Status::query()->select('id')->where('name', 'cancel')->first();
            $stages = [
                [
                    'name' => 'В работе',
                    'status_id' => $status_process->id,
                    'color' => '#0050FF'
                ],
                [
                    'name' => 'Выполнено',
                    'status_id' => $status_done->id,
                    'color' => '#28FC2A'
                ],
                [
                    'name' => 'Отменено',
                    'status_id' => $status_cancel->id,
                    'color' => '#FE3F6D'
                ],

            ];

            $pipeline->stages()->createMany($stages);
        });
    }

}
