<?php

namespace App\Models;

use App\Traits\SettingableTrait;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stage extends Model
{
    use CrudTrait;
    use HasFactory;
    use SettingableTrait;

    protected $fillable = [
        'name',
        'color',
        'pipeline_id',
    ];

    protected $guarded = ['id'];

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class);
    }

    public function pipeline(): BelongsTo
    {
        return $this->belongsTo(Pipeline::class);
    }
}
