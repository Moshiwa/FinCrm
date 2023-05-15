<?php

namespace App\Models;

use \Backpack\PermissionManager\app\Models\Role as BackpackRole;

class Role extends BackpackRole
{
    protected $table = 'roles';
    protected $connection = 'pgsql';

    protected $fillable = ['name', 'guard_name', 'updated_at', 'created_at'];
}
