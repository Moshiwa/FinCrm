<?php

namespace App\Models;

use App\Services\Space\SpaceService;
use App\Traits\SpaceableTrait;
use Illuminate\Database\Eloquent\Builder;
use \Backpack\PermissionManager\app\Models\Role as BackpackRole;

class Role extends BackpackRole
{
    use SpaceableTrait;

    protected $table = 'roles';
    protected $connection = 'pgsql';

    protected $fillable = ['name', 'guard_name', 'updated_at', 'created_at', 'space_id'];

    public function getTable()
    {
        return 'roles';
    }
}
