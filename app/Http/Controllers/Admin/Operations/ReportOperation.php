<?php

namespace App\Http\Controllers\Admin\Operations;

use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Route;

trait ReportOperation
{
    /**
     * Define which routes are needed for this operation.
     *
     * @param string $segment    Name of the current entity (singular). Used as first URL segment.
     * @param string $routeName  Prefix of the route name.
     * @param string $controller Name of the current CrudController.
     */
    protected function setupReportRoutes($segment, $routeName, $controller)
    {
        Route::get($segment, [
            'as'        => $routeName.'.report',
            'uses'      => $controller.'@report',
            'operation' => 'report',
        ]);
    }

    /**
     * Add the default settings, buttons, etc that this operation needs.
     */
    protected function setupReportDefaults()
    {
        CRUD::allowAccess('report');

        CRUD::operation('report', function () {
            CRUD::loadDefaultOperationSettingsFromConfig();
        });

        CRUD::operation('list', function () {
            // CRUD::addButton('top', 'report', 'view', 'crud::buttons.report');
            // CRUD::addButton('line', 'report', 'view', 'crud::buttons.report');
        });
    }

    /**
     * Show the view for performing the operation.
     *
     * @return Response
     */
    public function report()
    {
        CRUD::hasAccessOrFail('report');

        // prepare the fields you need to show
        $this->data['crud'] = $this->crud;
        $this->data['title'] = CRUD::getTitle() ?? 'Report '.$this->crud->entity_name;

        // load the view
        return view('crud::operations.report', $this->data);
    }
}
