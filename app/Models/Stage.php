<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stage extends Model
{
    use CrudTrait;
    use HasFactory;

    protected $table = 'stages';

    public $timestamps = false;

    protected $guarded = ['id'];

    public function status()
    {
        return $this->belongsTo(Status::class);
    }
}
