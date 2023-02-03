<?php

namespace App\Models;

use App\Traits\SpaceableTrait;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory,
        CrudTrait;
    use SpaceableTrait;

    public $timestamps = false;

    protected $fillable = ['name'];

}
