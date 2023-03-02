<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\TaskRequest;
use App\Models\Task;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class TaskCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\Task::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/task');
        CRUD::setEntityNameStrings('task', 'tasks');
    }

    protected function setupListOperation()
    {
        CRUD::column('name');
        CRUD::column('start');
        CRUD::column('end');
        CRUD::column('status');
    }

    protected function setupCreateOperation()
    {
        $now = now();
        CRUD::setValidation(TaskRequest::class);
        CRUD::field('name');
        CRUD::field('description')->default('');
        CRUD::field('start')->type('datetime')->default($now);
        CRUD::field('end')->type('datetime');
        CRUD::field('manager_id')->type('hidden')->default(backpack_user()->id);
        CRUD::field('executors')
            ->label('Исполнители')
            ->type('relationship');
        CRUD::field('status')->type('select_from_array')->options(Task::getStatuses());

    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
