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
        CRUD::setEntityNameStrings(__('entity.crud_titles.action.task_stage'), __('entity.crud_titles.many.task_stage'));
        if (! backpack_user()->can('task_stages.update')) {
            CRUD::denyAccess(['update']);
        }

        if (! backpack_user()->can('task_stages.delete')) {
            CRUD::denyAccess(['delete']);
        }

        if (! backpack_user()->can('task_stages.list')) {
            CRUD::denyAccess(['list']);
        }
    }

    protected function setupListOperation()
    {
        CRUD::column('name')->label('Наименование');
        CRUD::column('color')->label('Цвет')->type('color');
        CRUD::column('created_at')->label('Дата создания');
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(TaskStageRequest::class);

        CRUD::field('name')->label('Наименование');
        CRUD::field('color')->label('Цвет')->type('color')->wrapper([
            'class' => 'form-group col-md-1',
        ]);

    }


    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
