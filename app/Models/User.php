<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Services\Space\SpaceService;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use
        HasApiTokens,
        HasFactory,
        Notifiable,
        CrudTrait,
        HasRoles;

    protected $table = 'users';
    protected $connection = 'pgsql';
    protected $spaceAccessTmp;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function spaces(): BelongsToMany
    {
        return $this->belongsToMany(Space::class, 'user_spaces')
            ->using(UserSpace::class);
    }

    public function availableSpaces(): Collection
    {
        if ($this->isFirstUser())
            return Space::all();

        return $this->spaces()
            ->where('active', true)
            ->get();
    }

    public function isFirstUser() {
        return $this->id === User::query()->select('id')->orderBy('id','asc')->first()->id;
    }

    protected static function booted()
    {
        static::saved(function (self $user) {
            if(!is_null($user->spaceAccessTmp)) {
                $space = SpaceService::getCurrentSpaceModel();
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
