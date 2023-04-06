<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeadlineFormat extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'value'
    ];

    public $timestamps = false;

    public function getTrNameAttribute()
    {
        return __('deadline.format.' . $this->name);
    }
}
