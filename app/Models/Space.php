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
}
