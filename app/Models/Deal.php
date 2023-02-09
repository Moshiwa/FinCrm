<?php

namespace App\Models;

use App\Events\ChangePipeline;
use App\Events\ChangeStage;
use App\Listeners\ChangePipelineNotification;
use App\Traits\FieldableTrait;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Deal extends Model
{
    use CrudTrait;
    use HasFactory;
    use FieldableTrait;

    protected $guarded = ['id'];

    public function pipeline(): BelongsTo
    {
        return $this->belongsTo(Pipeline::class);
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(Stage::class);
    }

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(DealComment::class);
    }

    protected static function booted()
    {
        static::updating(function (self $deal) {
            if ($deal->isDirty('pipeline_id')) {
                $old_data = [
                    'pipeline_id' => $deal->getOriginal('pipeline_id'),
                    'stage_id' => $deal->getOriginal('stage_id')
                ];

                event(new ChangePipeline($deal, $old_data));
            }
            if ($deal->isDirty('stage_id')) {
                $old_data = [
                    'stage_id' => $deal->getOriginal('stage_id')
                ];

                event(new ChangeStage($deal, $old_data));
            }
        });
    }
}
