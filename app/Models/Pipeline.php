<?php

namespace App\Models;

use App\Services\Stage\StageService;
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
            $pipeline->stages()->createMany(StageService::getDefultStages());
        });
    }

}
