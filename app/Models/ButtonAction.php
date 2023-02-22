<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ButtonAction extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'stage_id',
        'pipeline_id',
        'responsible_id',
        'comment'
    ];
}
