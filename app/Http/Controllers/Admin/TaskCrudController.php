<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Operations\TaskOperation;
use App\Http\Requests\TaskRequest;
use App\Models\Task;
use App\Models\TaskStage;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class TaskCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use TaskOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\Task::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/task');
        CRUD::setEntityNameStrings('task', 'tasks');
    }

    protected function setupListOperation()
    {
        CRUD::addButton('top', 'pipelines', 'view', 'crud::buttons.task_create');

        CRUD::column('name');
        CRUD::column('start');
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(TaskRequest::class);
        CRUD::field('name');

        //CRUD::field('status')->type('select_from_array')->options(Task::getStatuses());

    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    public function taskCreate()
    {
        $stage = TaskStage::query()->first();
        $first_stage = $stage->id;
        $now = now();

        $task = Task::query()->create([
            'name' => 'Новая задача',
            'task_stage_id' => $first_stage,
            'description' => '',
            'start' => $now,
            'end' => null,
            'responsible_id' => backpack_user()->id,
            'manager_id' => null,
            'executor_id' => null,
        ]);

        return redirect('/admin/task/' . $task->id);
    }
}
