<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Models\Task;
use App\Models\TaskStage;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Route;

trait TaskOperation
{
    protected function setupTaskRoutes($segment, $routeName, $controller)
    {
        Route::get($segment.'/{id}/detail', [
            'as'        => $routeName.'.task',
            'uses'      => $controller.'@getTask',
            'operation' => 'task',
        ]);
    }

    protected function setupTaskDefaults()
    {
        CRUD::allowAccess('task');

        CRUD::operation('task', function () {
            CRUD::loadDefaultOperationSettingsFromConfig();
        });

        CRUD::operation('list', function () {
            CRUD::addButton('line', 'task', 'view', 'crud::buttons.task');
        });
    }

    public function getTask()
    {
        $task = $this->crud->getCurrentEntry();
        CRUD::hasAccessOrFail('task');
        $this->crud->setHeading('Задача');
        // prepare the fields you need to show
        $this->data['crud'] = $this->crud;
        $this->data['task'] = $task;
        $this->data['title'] = CRUD::getTitle() ?? 'Task '.$this->crud->entity_name;

        // load the view
        return view('crud::operations.task', $this->data);
    }
}
