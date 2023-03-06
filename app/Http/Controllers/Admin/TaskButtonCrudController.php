<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Operations\TaskButtonOperation;
use App\Http\Requests\TaskButtonRequest;
use App\Models\TaskButton;
use App\Models\TaskStage;
use App\Services\Button\ButtonService;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Arr;

class TaskButtonCrudController extends CrudController
{
    use TaskButtonOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\DealButton::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/button');
        CRUD::setEntityNameStrings('button', 'buttons');
    }

    public function save(TaskButtonRequest $request)
    {
        $data = $request->validated();

        $button = TaskButton::query();
        $options = [];

        if (empty($data['id'])) {
            $button = $button->create([
                'name' => $data['name'],
                'color' => $data['color'] ?? null,
                'icon' => $data['icon'] ?? null,
                'options' => $options
            ]);
        } else {
            $button = $button->find($data['id']);
            $button->update([
                'name' => $data['name'],
                'color' => $data['color'] ?? null,
                'icon' => $data['icon'] ?? null,
                'options' => $options
            ]);
        }

        $stages = Arr::pluck($data['visible'], 'id');
        $stages = array_filter($stages);
        $button->visible()->sync($stages);
        $button->action()->update([
            'task_stage_id' => $data['action']['task_stage_id'] ?? null,
            'responsible_id' => $data['action']['responsible_id'] ?? null,
            'manager_id' => $data['action']['manager_id'] ?? null,
            'executor_id' => $data['action']['executor_id'] ?? null,
            'comment' => $data['action']['comment'] ?? false,
            'start_time' => $data['action']['start_time'] ?? null,
            'end_time' => $data['action']['end_time'] ?? null,
        ]);

        $buttons = \App\Models\TaskButton::query()->with(['visible', 'action'])->get();
        $buttons = (new ButtonService())->mergeTaskButtonsSettings($buttons);

        return response()->json([
            'data' => [
                'buttons' => $buttons,
                'task_stages' => TaskStage::query()->select(['id', 'name'])->get(),
            ],
            'errors' => [],
        ]);
    }

    public function delete(TaskButton $button)
    {
        $button->delete();
    }
}
