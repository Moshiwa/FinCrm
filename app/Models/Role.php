<?php

namespace App\Models;

use App\Services\Space\SpaceService;
use App\Traits\ModelBaseConnectionTrait;
use Illuminate\Database\Eloquent\Builder;
use \Backpack\PermissionManager\app\Models\Role as BackpackRole;

class Role extends BackpackRole
{
    protected $table = 'roles';
    protected $connection = 'pgsql';

    protected $fillable = ['name', 'guard_name', 'updated_at', 'created_at'];

    protected static function booted()
    {
    }
}
