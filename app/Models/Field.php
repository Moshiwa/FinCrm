<?php

namespace App\Models;

use App\Enums\FieldsEntitiesEnum;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Field extends Model
{
    use CrudTrait;
    use HasFactory;

    public $timestamps = false;

    protected $guarded = ['id'];

    protected $fillable = [
        'type_id',
        'name',
        'entity',
        'options',
        'is_active'
    ];

    protected $with = [
        'type'
    ];

    protected $casts = [
        'options' => 'array',
        'entity' => FieldsEntitiesEnum::class
    ];

    public function type(): BelongsTo
    {
        return $this->belongsTo(FieldType::class);
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
