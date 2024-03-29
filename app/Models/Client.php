<?php

namespace App\Models;

use App\Enums\FieldsEntitiesEnum;
use App\Traits\FieldsTrait;
use App\Traits\SpaceableTrait;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Client extends Model
{
    use CrudTrait;
    use HasFactory;
    use SpaceableTrait;

    protected $guarded = ['id'];

    protected $appends = [
        'all_fields'
    ];

    public function fields(): BelongsToMany
    {
        return $this->belongsToMany(Field::class, 'client_fields')
            ->where('entity', FieldsEntitiesEnum::client->value)
            ->withPivot('value');
    }

    public function getAllFieldsAttribute(): Collection
    {
        $fields = $this->fields()->get();
        $all_fields = Field::includedClient()->get();

        foreach ($all_fields as $field) {
            foreach ($fields as $filled_field) {
                if ($filled_field->id === $field->id) {
                    continue(2);
                }
            }

            $fields->push($field);
        }

        return $fields;
    }

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class);
    }

    protected static function booted()
    {
    }
}
