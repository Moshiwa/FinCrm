<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Models\Field;
use App\Models\FieldClientSetting;
use App\Models\FieldSetting;
use App\Models\Pipeline;
use App\Models\Setting;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Route;

trait SettingsOperation
{
    /**
     * Define which routes are needed for this operation.
     *
     * @param string $segment    Name of the current entity (singular). Used as first URL segment.
     * @param string $routeName  Prefix of the route name.
     * @param string $controller Name of the current CrudController.
     */
    protected function setupSettingsRoutes($segment, $routeName, $controller)
    {
        Route::get($segment.'/', [
            'as'        => $routeName,
            'uses'      => $controller.'@settings',
            'operation' => 'settings',
        ]);
    }

    /**
     * Add the default settings, buttons, etc that this operation needs.
     */
    protected function setupSettingsDefaults()
    {
        CRUD::allowAccess('settings');

        CRUD::operation('settings', function () {
            CRUD::loadDefaultOperationSettingsFromConfig();
        });

        CRUD::operation('list', function () {
            // CRUD::addButton('top', 'settings', 'view', 'crud::buttons.settings');
            // CRUD::addButton('line', 'settings', 'view', 'crud::buttons.settings');
        });
    }

    /**
     * Show the view for performing the operation.
     *
     * @return Response
     */
    public function settings()
    {
        CRUD::hasAccessOrFail('settings');

        $entry = Pipeline::query()->orderBy('created_at', 'ASC')->get();

        $entry->load([
            'stages' => function ($query) {
                $query->orderBy('created_at', 'ASC');
            },
            'stages.settings',
        ]);

        // prepare the fields you need to show
        $this->data['crud'] = $this->crud;
        $this->data['entry'] = $entry;
        $this->data['settings'] = Setting::query()->get();
        $this->data['fields'] = Field::query()->with(['settings'])->get();
        $this->data['title'] = CRUD::getTitle() ?? 'Settings '.$this->crud->entity_name;

        // load the view
        return view('crud::settings', $this->data);
    }
}