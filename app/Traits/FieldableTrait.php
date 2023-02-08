<?php

namespace App\Traits;

use App\Models\Field;

trait FieldableTrait
{
    public function fields()
    {
        return static::morphToMany(Field::class, 'fieldable')->withPivot('value');
    }
}
