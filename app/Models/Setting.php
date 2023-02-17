<?php

namespace App\Models;

use App\Enums\SettingKeysEnum;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use CrudTrait;
    use HasFactory;

    public $timestamps = false;

    protected $casts = [
        'field' => 'array',
        'key' => SettingKeysEnum::class,
    ];

    protected $guarded = ['id'];
}
