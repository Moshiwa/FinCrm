<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use CrudTrait;
    use HasFactory;

    protected $guarded = ['id'];

    public function fields(): BelongsToMany
    {
        return $this->belongsToMany(Field::class, 'client_fields')
            ->where('entity', 'client')
            ->withPivot('value');
    }

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class);
    }
}
