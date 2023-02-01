<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    use CrudTrait;
    use HasFactory;

    //Types
    public const STRING = 'string';
    public const ARRAY = 'array';
    public const DATE = 'date';

    public $timestamps = false;

    protected $guarded = ['id'];

    protected $casts = [];

    public function getTypes(): array
    {
        return [ self::DATE, self::ARRAY, self::STRING ];
    }
}
