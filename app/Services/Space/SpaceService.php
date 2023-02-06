<?php

namespace App\Services\Space;

use App\Models\Space;
use Illuminate\Database\Eloquent\Builder;

class SpaceService
{
    public static function getCurrentSpace()
    {
        return Space::query()
            ->where('enable', true)
            ->first();
    }

    public static function enableSpace(Builder $space): void
    {
        if ($space->exists()) {
            Space::query()->update([
                'enable' => false
            ]);

            $space->update([
                'enable' => true
            ]);
        }
    }
}
