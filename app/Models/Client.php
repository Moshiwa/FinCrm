<?php

namespace App\Models;

use App\Traits\SpaceableTrait;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use CrudTrait;
    use HasFactory;
    use SpaceableTrait;

    protected $guarded = ['id'];
}
