<?php

namespace App\Models;

use App\Traits\ModelBaseConnectionTrait;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Backpack\PermissionManager\app\Models\Permission as BackpackPermission;
class Permission extends BackpackPermission
{
    use CrudTrait;

    protected $table = 'permissions';
    protected $connection = 'pgsql';

    protected $fillable = ['name', 'display_name', 'guard_name', 'group', 'updated_at', 'created_at'];

    public function getTable()
    {
        return 'permissions';
    }
}
