<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use CrudTrait;
    use HasFactory;

    protected $guarded = ['id'];

    public function fields()
    {
        return $this
            ->belongsToMany(Field::class, 'client_fields', 'client_id', 'field_id')
            ->withPivot('value');
    }

    /*public function fields()
    {
        return $this->belongsToMany(
            'App\ProductFields',
            'product_field_relationships',
            'product_id',
            'product_field_id'
        );
    }*/
}
