<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskStage extends Model
{
    use HasFactory;
    use CrudTrait;

    protected $fillable = [
        'name',
        'color'
    ];
}
