<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    use CrudTrait;
    use HasFactory;

    public $timestamps = false;

    protected $guarded = ['id'];

    protected $casts = [];
}
