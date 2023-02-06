<?php

namespace App\Traits;

use App\Models\Space;
use App\Services\Space\SpaceService;
use Illuminate\Database\Eloquent\Builder;

trait SpaceableTrait
{
    public function spaces()
    {
        return static::morphToMany(Space::class, 'spaceable');
    }

    protected static function bootSpaceableTrait()
    {
        static::created(function ($model) {
            $space_id = SpaceService::getCurrentSpace()->id;
            $model->load('spaces');
            $model->spaces()->sync($space_id);
        });
        static::addGlobalScope('spaces', function(Builder $builder) {
            $space_id = SpaceService::getCurrentSpace()->id;
            $builder->whereHas('spaces', function($query) use ($space_id) {
                $query->where('spaces.id', $space_id);
            });
        });
    }
}
