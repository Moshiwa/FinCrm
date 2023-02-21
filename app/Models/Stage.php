<?php

namespace App\Models;

use App\Services\Space\SpaceService;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stage extends Model
{
    use CrudTrait;
    use HasFactory;

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

    public function settings(): belongsToMany
    {
        return $this->belongsToMany(Setting::class, 'settings_stages')
            ->withPivot('value');
    }

    public function pipeline(): BelongsTo
    {
        return $this->belongsTo(Pipeline::class);
    }

    public function buttons()
    {
        return $this->belongsToMany(
            Button::class,
            'button_stages',
            'stage_id',
            'button_id'
        );
    }

    public function getUrlSettingAttribute()
    {
        return "<a class='btn btn-outline-primary' href='/admin/stage/" . $this->id . "/edit'>Настройки</a>";
    }
}
