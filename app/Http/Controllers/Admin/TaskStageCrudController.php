<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\TaskStageRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class TaskStageCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\TaskStage::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/task-stage');
        CRUD::setEntityNameStrings('task stage', 'task stages');
    }

    protected function setupListOperation()
    {
        CRUD::column('name');
        CRUD::column('color')->type('color');
        CRUD::column('created_at');
        CRUD::column('updated_at');
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(TaskStageRequest::class);

        CRUD::field('name');
        CRUD::field('color')->type('color')->wrapper([
            'class' => 'form-group col-md-1',
        ]);

    }


    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
