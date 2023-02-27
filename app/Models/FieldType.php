<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FieldType extends Model
{
    use HasFactory;
    use CrudTrait;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'description'
    ];

    public function getTrNameAttribute()
    {
        return __('fields.type.' . $this->name);
    }
}
