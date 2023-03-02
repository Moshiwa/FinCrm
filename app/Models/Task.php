<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;
    use CrudTrait;

    protected $fillable = [
        'name',
        'task_stage_id',
        'description',
        'start',
        'end',
        'responsible_id',
        'manager_id',
        'executor_id',
    ];

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function executor(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    public function status(): BelongsTo
    {
        return $this->belongsTo(TaskStage::class);
    }

    public function fields(): BelongsToMany
    {
        return $this->belongsToMany(Field::class, 'task_fields')
            ->where('entity', 'task')
            ->withPivot('value');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TaskComment::class);
    }


}
