<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use CrudTrait;
    use HasFactory;

    protected $table = 'settings';
    // protected $primaryKey = 'id';
    public $timestamps = false;
    protected $guarded = ['id'];

    public function scopeDisplayInDeal(Builder $builder)
    {
        return $builder
            ->where('name', 'display-in-deal')
            ->where('type', 'field');
    }

    public function scopeDisplayInClient(Builder $builder)
    {
        return $builder
            ->where('name', 'display-in-client')
            ->where('type', 'field');
    }
}
