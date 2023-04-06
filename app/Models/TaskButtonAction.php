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
        'comment',
        'deadline_format_id',
        'deadline_value',
    ];

    protected $appends = [
        'deadline'
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

    public function deadline_format(): BelongsTo
    {
        return $this->belongsTo(DeadlineFormat::class);
    }

    public function getDeadlineAttribute(): string|int
    {
        $value = (int)$this->deadline_value ?? 0;
        $format = (int)$this->deadline_format?->value ?? 0;

        $result = $value * $format;

        return empty($result) ? '' : $result;
    }
}
