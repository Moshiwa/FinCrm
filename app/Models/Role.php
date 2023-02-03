<?php

namespace App\Models;

use App\Services\Space\SpaceService;
use App\Traits\ModelBaseConnectionTrait;
use App\Traits\SpaceableTrait;
use Illuminate\Database\Eloquent\Builder;
use \Backpack\PermissionManager\app\Models\Role as BackpackRole;

class Role extends BackpackRole
{
    use ModelBaseConnectionTrait;
    use SpaceableTrait;

    protected $table = 'roles';
    protected $connection = 'pgsql';

    protected $fillable = ['name', 'guard_name', 'updated_at', 'created_at', 'space_id'];

    public function getTable()
    {
        return 'roles';
    }

    public function space(){
        return $this->belongsTo(Space::class);
    }

    protected static function booted()
    {
        static::addGlobalScope('space', function (Builder $builder) {
            $space = SpaceService::getCurrentSpaceModel();
            $builder->where(function (Builder $builder) use ($space) {
                $builder->whereNull('space_id');
                $builder->orWhere('space_id', $space->id);
            });
        });

        static::creating(function (self $item) {
            $space = SpaceService::getCurrentSpaceModel();
            $item->space_id = $space ? $space->id : null;
        });
    }
}
