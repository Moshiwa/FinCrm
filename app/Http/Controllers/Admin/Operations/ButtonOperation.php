<?php

namespace App\Http\Controllers\Admin\Operations;

use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Route;

trait ButtonOperation
{
    protected function setupButtonRoutes($segment, $routeName, $controller)
    {
        Route::get($segment, [
            'as'        => $routeName.'.button',
            'uses'      => $controller.'@button',
            'operation' => 'button',
        ]);
    }

    protected function setupButtonDefaults()
    {
        CRUD::allowAccess('button');

        CRUD::operation('button', function () {
            CRUD::loadDefaultOperationSettingsFromConfig();
        });

    }

    public function button()
    {
        CRUD::hasAccessOrFail('button');

        // prepare the fields you need to show
        $this->data['crud'] = $this->crud;
        $this->data['title'] = CRUD::getTitle() ?? 'Button '.$this->crud->entity_name;

        // load the view
        return view('crud::operations.button', $this->data);
    }
}
