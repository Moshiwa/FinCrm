<?php

namespace App\Http\Controllers\Admin\Operations;

use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Route;

trait ButtonOperation
{
    /**
     * Define which routes are needed for this operation.
     *
     * @param string $segment    Name of the current entity (singular). Used as first URL segment.
     * @param string $routeName  Prefix of the route name.
     * @param string $controller Name of the current CrudController.
     */
    protected function setupButtonRoutes($segment, $routeName, $controller)
    {
        Route::get($segment, [
            'as'        => $routeName.'.button',
            'uses'      => $controller.'@button',
            'operation' => 'button',
        ]);
    }

    /**
     * Add the default settings, buttons, etc that this operation needs.
     */
    protected function setupButtonDefaults()
    {
        CRUD::allowAccess('button');

        CRUD::operation('button', function () {
            CRUD::loadDefaultOperationSettingsFromConfig();
        });

    }

    /**
     * Show the view for performing the operation.
     *
     * @return Response
     */
    public function button()
    {
        CRUD::hasAccessOrFail('button');

        // prepare the fields you need to show
        $this->data['crud'] = $this->crud;
        $this->data['title'] = CRUD::getTitle() ?? 'Button '.$this->crud->entity_name;

        // load the view
        return view('crud::button', $this->data);
    }
}
