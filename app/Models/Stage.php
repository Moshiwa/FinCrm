<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stage extends Model
{
    use CrudTrait;
    use HasFactory;

    protected $fillable = [
        'name',
        'color',
        'status_id',
        'pipeline_id',
    ];

    protected $guarded = ['id'];

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function pipeline(): BelongsTo
    {
        return $this->belongsTo(Pipeline::class);
    }
}
