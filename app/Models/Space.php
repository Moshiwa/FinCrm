<?php

namespace App\Models;

use App\Services\Space\SpaceService;
use App\Traits\ModelBaseConnectionTrait;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;

class Space extends Model
{
    use CrudTrait;
    use ModelBaseConnectionTrait;

    protected $table = 'spaces';
    protected $guarded = ['id'];

    protected static function booted()
    {
        static::created(function (self $space) {
            SpaceService::prepareAllSpaceConnections();
            Artisan::call('migrate2', ['space' => $space->code]);
            SpaceService::prepareAllUploadDirectories();
        });
        static::deleted(function (self $space) {
            SpaceService::addSpaceConnections($space->code);
            Artisan::call('migrate2:reset', ['space' => $space->code]);
            SpaceService::prepareAllSpaceConnections();
            SpaceService::removeSpaceUploadDirectory($space->code);
        });
    }
}
