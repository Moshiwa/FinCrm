<?php

namespace App\Http\Controllers\Admin\Operations;

use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Route;

trait TaskButtonOperation
{
    protected function setupTaskButtonRoutes($segment, $routeName, $controller)
    {
        Route::get($segment, [
            'as'        => $routeName.'.taskButton',
            'uses'      => $controller.'@taskButton',
            'operation' => 'taskButton',
        ]);
    }

    protected function setupTaskButtonDefaults()
    {
        CRUD::allowAccess('taskButton');

        CRUD::operation('taskButton', function () {
            CRUD::loadDefaultOperationSettingsFromConfig();
        });

        CRUD::operation('list', function () {
            // CRUD::addButton('top', 'task_button', 'view', 'crud::buttons.task_button');
            // CRUD::addButton('line', 'task_button', 'view', 'crud::buttons.task_button');
        });
    }

    public function taskButton()
    {
        CRUD::hasAccessOrFail('taskButton');

        // prepare the fields you need to show
        $this->data['crud'] = $this->crud;
        $this->data['title'] = CRUD::getTitle() ?? 'Task Button '.$this->crud->entity_name;

        // load the view
        return view('crud::operations.task_button', $this->data);
    }
}
