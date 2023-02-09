<?php

namespace App\Http\Controllers\Admin\Operations;

use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Route;

trait DealSettingOperation
{
    /**
     * Define which routes are needed for this operation.
     *
     * @param string $segment    Name of the current entity (singular). Used as first URL segment.
     * @param string $routeName  Prefix of the route name.
     * @param string $controller Name of the current CrudController.
     */
    protected function setupDealSettingsRoutes($segment, $routeName, $controller)
    {
        Route::get($segment.'/deal-settings', [
            'as'        => $routeName.'.dealSettings',
            'uses'      => $controller.'@dealSettings',
            'operation' => 'dealSettings',
        ]);
    }

    /**
     * Add the default settings, buttons, etc that this operation needs.
     */
    protected function setupDealSettingsDefaults()
    {
        CRUD::allowAccess('dealSettings');

        CRUD::operation('dealSettings', function () {
            CRUD::loadDefaultOperationSettingsFromConfig();
        });

        CRUD::operation('list', function () {
            // CRUD::addButton('top', 'deal_settings', 'view', 'crud::buttons.deal_settings');
            // CRUD::addButton('line', 'deal_settings', 'view', 'crud::buttons.deal_settings');
        });
    }

    /**
     * Show the view for performing the operation.
     *
     * @return Response
     */
    public function dealSettings()
    {
        CRUD::hasAccessOrFail('dealSettings');

        // prepare the fields you need to show
        $this->data['crud'] = $this->crud;
        $this->data['title'] = CRUD::getTitle() ?? 'Deal Settings '.$this->crud->entity_name;

        // load the view
        return view('crud::operations.deal_settings', $this->data);
    }
}
