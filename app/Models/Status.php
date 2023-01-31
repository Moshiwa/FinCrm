<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory,
        CrudTrait;

    protected $table = 'statuses';

    public $timestamps = false;

    protected $fillable = ['name'];

}
