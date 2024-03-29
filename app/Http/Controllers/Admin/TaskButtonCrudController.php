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
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\DealButton::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/button');
        CRUD::setEntityNameStrings(__('entity.crud_titles.action.button_task'), __('entity.crud_titles.many.button_task'));
        if (! backpack_user()->can('task_buttons.list')) {
            CRUD::denyAccess(['list']);
        }
    }

    public function index()
    {
        $this->crud->hasAccessOrFail('list');

        $this->data['crud'] = $this->crud;
        $this->data['title'] = $this->crud->getTitle() ?? mb_ucfirst($this->crud->entity_name_plural);

        // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
        return view('crud::detail_task_button', $this->data);
    }

    public function save(TaskButtonRequest $request)
    {
        $data = $request->validated();

        $button = TaskButton::query();
        $options = [];

        if (empty($data['id'])) {
            if (! backpack_user()->can('task_buttons.create')) {
                return response()->json([
                    'success' => false,
                    'message' => 'У вас недостаточно прав'
                ], 403);
            }

            $button = $button->create([
                'name' => $data['name'],
                'color' => $data['color'] ?? null,
                'icon' => $data['icon'] ?? null,
                'options' => $options
            ]);
        } else {
            if (! backpack_user()->can('task_buttons.update')) {
                return response()->json([
                    'success' => false,
                    'message' => 'У вас недостаточно прав'
                ], 403);
            }

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
            'deadline_value' => $data['action']['deadline_value'] ?? null,
            'deadline_format_id' => $data['action']['deadline_format_id'] ?? null,
        ]);

        $buttons = TaskButton::query()->with(['visible', 'action'])->get();
        $buttons = (new ButtonService())->mergeTaskButtonsSettings($buttons);

        return response()->json([
            'data' => [
                'buttons' => $buttons,
                'task_stages' => TaskStage::query()->select(['id', 'name'])->get(),
            ],
        ]);
    }

    public function delete(TaskButton $button)
    {
        if (! backpack_user()->can('task_buttons.delete')) {
            return response()->json([
                'success' => false,
                'message' => 'У вас недостаточно прав'
            ], 403);
        }

        $button->delete();

        return response()->json([
            'success' => true,
        ]);
    }
}
