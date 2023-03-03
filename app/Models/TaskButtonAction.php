<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskButtonAction extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'task_stage_id',
        'responsible_id',
        'manager_id',
        'executor_id',
        'start_time',
        'end_time',
        'comment'
    ];

    public function stage(): BelongsTo
    {
        return $this->belongsTo(TaskStage::class);
    }

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
}
