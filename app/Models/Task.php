<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Task extends Model
{
    use HasFactory;
    use CrudTrait;

    protected $fillable = [
        'name',
        'description',
        'deadline',
        'status'
    ];

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function executors(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'task_executors',
            'task_id',
            'executor_id'
        );
    }


}
