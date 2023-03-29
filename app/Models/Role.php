<?php

namespace App\Models;

use App\Services\Space\SpaceService;
use App\Traits\ModelBaseConnectionTrait;
use Illuminate\Database\Eloquent\Builder;
use \Backpack\PermissionManager\app\Models\Role as BackpackRole;

class Role extends BackpackRole
{
    use ModelBaseConnectionTrait;

    protected $table = 'roles';
    protected $connection = 'pgsql';

    protected $fillable = ['name', 'guard_name', 'updated_at', 'created_at'];

    public function getTable()
    {
        return 'roles';
    }

    protected static function booted()
    {
    }
}
