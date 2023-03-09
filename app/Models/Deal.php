<?php

namespace App\Models;

use App\Events\ChangePipeline;
use App\Events\ChangeResponsible;
use App\Events\ChangeStage;
use App\Events\CreateDeal;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Deal extends Model
{
    use CrudTrait;
    use HasFactory;

    protected $guarded = ['id'];

    protected $fillable = [
        'name',
        'pipeline_id',
        'stage_id',
        'responsible_id',
    ];

    public function fields()
    {
        return $this->belongsToMany(Field::class, 'deal_fields')
            ->where('entity', 'deal')
            ->withPivot('value');
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

    protected static function booted()
    {
        /*static::creating(function (self $task) {
            if (! backpack_user()->can('deals.create')) {
                abort(403, 'У вас недостаточно прав');
            }
        });
        static::updating(function (self $task) {
            if (! backpack_user()->can('deals.edit')) {
                abort(403, 'У вас недостаточно прав');
            }
        });*/
        static::created(function (self $deal) {
            event(new CreateDeal($deal));
        });
    }
}
