<?php

namespace App\Models;

use App\Services\Space\SpaceService;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\Storage;

class Space extends Model
{
    use CrudTrait;

    protected $table = 'spaces';
    protected $guarded = ['id'];

    public function deals(): MorphToMany
    {
        return static::morphedByMany(Deal::class, 'spaceable');
    }

    public function tasks(): MorphToMany
    {
        return static::morphedByMany(Task::class, 'spaceable');
    }

    public function clients(): MorphToMany
    {
        return static::morphedByMany(Client::class, 'spaceable');
    }

    public function fields(): MorphToMany
    {
        return static::morphedByMany(Field::class, 'spaceable');
    }

    public function pipelines(): MorphToMany
    {
        return static::morphedByMany(Pipeline::class, 'spaceable');
    }

    public function stages(): MorphToMany
    {
        return static::morphedByMany(Stage::class, 'spaceable');
    }

    public function task_stages(): MorphToMany
    {
        return static::morphedByMany(TaskStage::class, 'spaceable');
    }

    public function deal_buttons(): MorphToMany
    {
        return static::morphedByMany(DealButton::class, 'spaceable');
    }

    public function task_buttons(): MorphToMany
    {
        return static::morphedByMany(TaskButton::class, 'spaceable');
    }

    protected static function booted()
    {
        static::deleting(function ($space) {
            self::deleteSubRelations($space);
            SpaceService::setCurrentSpaceCode(SpaceService::$default_space_code);
        });

        static::created(function ($space) {
            $pipeline = self::createDefaultPipeline($space);
            $stages = self::createDefaultStages($space, $pipeline);
            $task_stages = self::createDefaultTaskStage($space);
        });
    }

    private static function createDefaultPipeline($space): Model|Builder
    {
        $pipeline = Pipeline::query()->create(['name' => 'Основная']);
        $pipeline->spaces()->sync($space->id);
        return $pipeline;
    }

    private static function createDefaultStages($space, $pipeline): array
    {
        $stages = [
            [
                'name' => 'В работе',
                'pipeline_id' => $pipeline->id,
                'color' => '#0050FF'
            ],
            [
                'name' => 'Выполнено',
                'pipeline_id' => $pipeline->id,
                'color' => '#28FC2A'
            ],
            [
                'name' => 'Отменено',
                'pipeline_id' => $pipeline->id,
                'color' => '#FE3F6D'
            ],
        ];

        $result = [];

        foreach ($stages as $stage) {
            $created_stage = Stage::query()->create($stage);
            $created_stage->spaces()->sync($space->id);

            $result[] = $created_stage;
        }

        return $result;
    }

    private static function createDefaultTaskStage($space): array
    {
        $task_stages = [
            [
                'name' => 'Новая',
                'color' => '#0c84e4'
            ],
            [
                'name' => 'Завершена',
                'color' => '#04AA6D'
            ]
        ];

        $result = [];
        foreach ($task_stages as $task_stage) {
            $created_task_stage = TaskStage::query()->create($task_stage);
            $created_task_stage->spaces()->sync($space->id);

            $result[] = $created_task_stage;
        }

        return $result;
    }

    private static function deleteSubRelations($space)
    {
        $space->deals->each->delete();
        $space->clients->each->delete();
        $space->stages->each->delete();
        $space->pipelines->each->delete();
        $space->deal_buttons->each->delete();
        $space->tasks->each->delete();
        $space->task_stages->each->delete();
        $space->task_buttons->each->delete();
        $space->fields->each->delete();

        $deal_dir = 'deal_' . SpaceService::getCurrentSpaceCode();
        if(Storage::disk('public')->exists($deal_dir)){
            Storage::disk('public')->deleteDirectory($deal_dir);
        }

        $task_dir = 'task_' . SpaceService::getCurrentSpaceCode();
        if(Storage::disk('public')->exists($task_dir)){
            Storage::disk('public')->deleteDirectory($task_dir);
        }
    }
}
