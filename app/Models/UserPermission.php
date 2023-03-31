<?php

namespace App\Models;

use App\Services\Space\SpaceService;
use App\Traits\SpaceableTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphPivot;

final class UserPermission extends MorphPivot
{
    protected static function booted()
    {
        /*static::addGlobalScope('space', function (Builder $builder) {
            $space = SpaceService::getCurrentSpaceModel();
            $builder->where(function (Builder $builder) use ($space) {
                $builder->whereNull('space_id');
                $builder->orWhere('space_id', $space->id);
            });
        });

        static::creating(function (self $item) {
            $space = SpaceService::getCurrentSpaceModel();
            $item->space_id = $space ? $space->id : null;
        });*/
    }
}
