<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use CrudTrait;
    use HasFactory;

    public $timestamps = false;

    protected $casts = [
        'field' => 'array'
    ];

    protected $guarded = ['id'];
}
