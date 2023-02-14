<?php

namespace App\Models;

use App\Traits\FieldableTrait;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Client extends Model
{
    use CrudTrait;
    use HasFactory;
    //use FieldableTrait;

    protected $guarded = ['id'];

    public function fields()
    {
        return $this->belongsToMany(Field::class, 'client_fields')
            ->where('entity', 'client')
            ->withPivot('value');
    }

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class);
    }

    protected static function booted()
    {
        static::deleting(function (self $client) {
            $client->deals()->delete();
        });
    }
}
