<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Space extends Model
{
    use CrudTrait;

    protected $table = 'spaces';
    protected $guarded = ['id'];

    protected static function booted()
    {
        static::deleting(function ($model) {
            if ($model->active) {
                $first_space = Space::query()
                    ->where('id', '<>', $model->id)
                    ->first();
                $first_space->update(['active' => true]);
            }
        });
    }
}
