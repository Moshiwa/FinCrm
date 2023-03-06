<?php

namespace App\Http\Controllers\Admin\Operations;

use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Route;

trait ManagerActionsInfoOperation
{
    protected function setupManagerActionsInfoRoutes($segment, $routeName, $controller)
    {
        Route::get($segment.'/manager-actions-info/{id}', [
            'as'        => $routeName.'.managerActionsInfo',
            'uses'      => $controller.'@managerActionsInfo',
            'operation' => 'managerActionsInfo',
        ]);
    }

    protected function setupManagerActionsInfoDefaults()
    {
        CRUD::allowAccess('managerActionsInfo');

        CRUD::operation('managerActionsInfo', function () {
            CRUD::loadDefaultOperationSettingsFromConfig();
        });

        CRUD::operation('list', function () {
            // CRUD::addButton('top', 'manager_actions_info', 'view', 'crud::buttons.manager_actions_info');
            // CRUD::addButton('line', 'manager_actions_info', 'view', 'crud::buttons.manager_actions_info');
        });
    }

    public function managerActionsInfo()
    {
        CRUD::hasAccessOrFail('managerActionsInfo');
        $user = $this->crud->getCurrentEntry();
        // prepare the fields you need to show
        $this->data['crud'] = $this->crud;
        $this->data['user'] = $user;
        $this->data['title'] = CRUD::getTitle() ?? 'Manager Actions Info '.$this->crud->entity_name;

        // load the view
        return view('crud::operations.manager_actions_info', $this->data);
    }
}
