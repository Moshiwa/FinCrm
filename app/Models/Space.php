<?php

namespace App\Models;

use App\Services\Space\SpaceService;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

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
            if ($space->active) {
                $first_space = Space::query()
                    ->where('id', '<>', $space->id)
                    ->first();
                $first_space->update(['active' => true]);
            }

            $this->deleteSubRelations($space);

            SpaceService::setCurrentSpaceCode(SpaceService::$default_space_code);
        });

        static::created(function ($space) {
            $pipeline = $this->createDefaultPipeline($space);
            $stages = $this->createDefaultStages($space, $pipeline);
            $task_stages = $this->createDefaultTaskStage($space);
        });
    }

    private function createDefaultPipeline($space): Model|Builder
    {
        $pipeline = Pipeline::query()->create(['name' => 'Основная']);
        $pipeline->spaces()->sync($space->id);
        return $pipeline;
    }

    private function createDefaultStages($space, $pipeline): array
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

    private function createDefaultTaskStage($space): array
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

    private function deleteSubRelations($space)
    {
        $ids = $space->deals->pluck('id');
        $space->deals()->whereIn('spaceable_id', $ids)->delete();
        Deal::query()->whereIn('id', $ids)->delete();

        $ids = $space->clients->pluck('id');
        $space->clients()->whereIn('spaceable_id', $ids)->delete();
        Client::query()->whereIn('id', $ids)->delete();

        $ids = $space->stages->pluck('id');
        $space->stages()->whereIn('spaceable_id', $ids)->delete();
        Stage::query()->whereIn('id', $ids)->delete();

        $ids = $space->pipelines->pluck('id');
        $space->pipelines()->whereIn('spaceable_id', $ids)->delete();
        Pipeline::query()->whereIn('id', $ids)->delete();

        $ids = $space->deal_buttons->pluck('id');
        $space->deal_buttons()->whereIn('spaceable_id', $ids)->delete();
        DealButton::query()->whereIn('id', $ids)->delete();

        $ids = $space->tasks->pluck('id');
        $space->tasks()->whereIn('spaceable_id', $ids)->delete();
        Task::query()->whereIn('id', $ids)->delete();

        $ids = $space->task_stages->pluck('id');
        $space->task_stages()->whereIn('spaceable_id', $ids)->delete();
        TaskStage::query()->whereIn('id', $ids)->delete();

        $ids = $space->task_buttons->pluck('id');
        $space->task_buttons()->whereIn('spaceable_id', $ids)->delete();
        TaskButton::query()->whereIn('id', $ids)->delete();

        $ids = $space->fields->pluck('id');
        $space->fields()->whereIn('spaceable_id', $ids)->delete();
        Field::query()->whereIn('id', $ids)->delete();
    }
}
