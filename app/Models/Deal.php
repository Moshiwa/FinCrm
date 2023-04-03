<?php

namespace App\Models;

use App\Enums\FieldsEntitiesEnum;
use App\Events\ChangePipeline;
use App\Events\ChangeResponsible;
use App\Events\ChangeStage;
use App\Events\CreateDeal;
use App\Traits\SpaceableTrait;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class Deal extends Model
{
    use CrudTrait;
    use HasFactory;
    use SpaceableTrait;

    protected $guarded = ['id'];

    protected $fillable = [
        'name',
        'pipeline_id',
        'stage_id',
        'responsible_id',
    ];

    protected $appends = [
        'all_fields'
    ];

    public function fields()
    {
        return $this->belongsToMany(Field::class, 'deal_fields')
            ->where('entity', FieldsEntitiesEnum::deal->value)
            ->withPivot('value');
    }

    public function getAllFieldsAttribute(): Collection
    {
        $fields = $this->fields()->get();
        $all_fields = Field::includedDeal()->get();

        foreach ($all_fields as $field) {
            foreach ($fields as $filled_field) {
                if ($filled_field->id === $field->id) {
                    continue(2);
                }
            }

            $fields->push($field);
        }

        return $fields;
    }

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

    public function comments(): morphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public static function booted()
    {
        parent::boot();
        static::created(function (self $deal) {
            event(new CreateDeal($deal));
        });
        static::deleting(function (self $deal) {
            $deal->comments->each->delete();
        });
    }
}
