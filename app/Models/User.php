<?php

namespace App\Models;

use App\Services\Space\SpaceService;
use App\Traits\ModelBaseConnectionTrait;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, CrudTrait;
    use TwoFactorAuthenticatable;

    protected $table = 'users';
    protected $connection = 'pgsql';
    protected $spaceAccessTmp;
    protected $appends = ['permission_names'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'two_factor_recovery_codes',
        'two_factor_secret',
        'two_factor_confirmed_at',
        'spaceAccess',
        'uiscom_token',
        'uiscom_employee_id',
        'uiscom_virtual_number',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'spaceAccess' => 'boolean',
        'email_verified_at' => 'datetime',
        'two_factor_confirmed_at' => 'datetime',
    ];

    public function getAllPermissionsAttribute(): Collection
    {
        $permissions = collect($this->permissions()->get());
        $roles = $this->roles()->with('permissions')->get();
        foreach ($roles as $role) {
            $permissions = $permissions->concat($role->permissions);
        }
        return $permissions;
    }

    public function getPermissionNamesAttribute()
    {
        return $this->getAllPermissionsAttribute()->pluck('name');
    }

    public function spaces()
    {
        return $this->belongsToMany(Space::class, 'user_spaces')
            ->using(UserSpace::class);
    }

    public function comments(): hasMany
    {
        return $this->hasMany(Comment::class, 'author_id');
    }

    public function execute_tasks(): hasMany
    {
        return $this->hasMany(Task::class, 'executor_id');
    }

    public function manage_tasks(): hasMany
    {
        return $this->hasMany(Task::class, 'manager_id');
    }

    public function responsible_tasks(): hasMany
    {
        return $this->hasMany(Task::class, 'responsible_id');
    }

    public function deals(): hasMany
    {
        return $this->hasMany(Deal::class, 'responsible_id');
    }

    public function setSpaceAccessAttribute($value) {
        $this->spaceAccessTmp = $value;
    }

    public function isAdminUser(): bool
    {
        $admins = User::query()->whereHas('roles', function($q) { $q->where('name', 'admin'); })->get();
        foreach ($admins as $admin) {
            if ($this->id === $admin->id) {
                return true;
            }
        }

        return false;
    }

    public function availableSpaces(): Collection
    {
        //Все админы получают доступ ко всем организациям
        if ($this->isAdminUser()) {
            return Space::all();
        }

        return $this->spaces()
            ->where('active', true)
            ->get();
    }

    public function canAccessCurrentSpace(): bool
    {
        return $this->availableSpaces()
                ->where('code', SpaceService::getCurrentSpaceCode())
                ->count() > 0;
    }

    public function permissions(): BelongsToMany
    {
        $space = SpaceService::getCurrentSpace();
        $relation = $this->morphToMany(
            config('permission.models.permission'),
            'model',
            config('permission.table_names.model_has_permissions'),
            config('permission.column_names.model_morph_key'),
            PermissionRegistrar::$pivotPermission
        )
            ->using(UserPermission::class)
            ->wherePivot('space_id', $space->id);

        if (! PermissionRegistrar::$teams) {
            return $relation;
        }

        return $relation->wherePivot(PermissionRegistrar::$teamsKey, getPermissionsTeamId());
    }

    protected static function booted()
    {
        static::saved(function (self $user) {
            if(!is_null($user->spaceAccessTmp)) {
                $space = SpaceService::getCurrentSpace();
                if((int) $user->spaceAccessTmp) {
                    if($user->spaces()->where('space_id', $space->id)->count() == 0) {
                        $user->spaces()->attach($space->id);
                    }
                } else {
                    $user->spaces()->detach($space->id);
                }
            }
        });
    }
}
