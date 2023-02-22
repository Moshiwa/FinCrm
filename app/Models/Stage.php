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

    public function getUrlSettingAttribute()
    {
        return "<a class='btn btn-outline-primary' href='/admin/stage/" . $this->id . "/edit'>Настройки</a>";
    }

    protected static function booted()
    {
        static::created(function (self $stage) {
            $stage->with(['pipeline.buttons' => ['visible', 'action']]);
            foreach ($stage->pipeline->buttons as $button) {
                if (empty($button->is_default)) {
                    continue;
                }

                $button->visible()->attach($stage->id);
            }
        });
    }
}
