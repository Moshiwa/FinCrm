<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\belongsTo;

class FieldDealSetting extends Model
{
    protected $guarded = ['id'];

    public function field(): belongsTo
    {
        return $this->belongsTo(Field::class);
    }
}
