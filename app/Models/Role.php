<?php

namespace App\Models;

use App\Traits\SpaceableTrait;
use \Backpack\PermissionManager\app\Models\Role as BackpackRole;

class Role extends BackpackRole
{
    use SpaceableTrait;

    protected $table = 'roles';
    protected $connection = 'pgsql';

    protected $fillable = ['name', 'guard_name', 'updated_at', 'created_at'];
}
