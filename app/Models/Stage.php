<?php

namespace App\Models;

use App\Traits\SpaceableTrait;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stage extends Model
{
    use CrudTrait;
    use HasFactory;
    use SpaceableTrait;

    public $timestamps = false;

    protected $guarded = ['id'];

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function pipeline(): BelongsTo
    {
        return $this->belongsTo(Pipeline::class);
    }

    protected static function booted()
    {
        static::saving(function (self $stage) {
            /*Stage::query()->update([''])
            $stage->
            dd(12);*/
        });
    }
}
