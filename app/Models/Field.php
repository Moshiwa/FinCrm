<?php

namespace App\Models;

use App\Enums\FieldsEntitiesEnum;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    use CrudTrait;
    use HasFactory;

    public $timestamps = false;

    protected $guarded = ['id'];

    protected $fillable = [
        'type',
        'name',
        'entity',
        'options',
        'is_active'
    ];

    protected $casts = [
        'options' => 'array'
    ];

    public function clients()
    {
        return $this->belongsToMany(Client::class, 'fieldable')
            ->withPivot('is_enable');
    }

    public function deals()
    {
        return $this->morphedByMany(Deal::class, 'fieldable')
            ->withPivot('is_enable');
    }

    public function scopeIncludedClient(Builder $builder)
    {
        return $builder
            ->where('entity', FieldsEntitiesEnum::client->value)
            ->where('is_active', true);
    }

    public function scopeIncludedDeal(Builder $builder)
    {
        return $builder
            ->where('entity', FieldsEntitiesEnum::deal->value)
            ->where('is_active', true);
    }
}
