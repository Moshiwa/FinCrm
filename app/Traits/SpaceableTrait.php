<?php

namespace App\Traits;

use App\Models\Space;

trait SpaceableTrait
{
    public function spaces()
    {
        return static::morphToMany(Space::class, 'spaceable');
    }

    public function scopeSpace($query)
    {
        $space = Space::query()->where('enable', true)->first();
        $space_id = $space->id;

        return $query->whereHas('spaces', function ($query) use ($space_id) {
            $query->where('spaces.id', $space_id);
        });
    }

    protected static function bootSpaceableTrait()
    {
        static::created(function ($model) {
            $space = Space::query()->where('enable', true)->first();
            $model->load('spaces');
            $model->spaces()->sync($space->id);
        });
    }
}
