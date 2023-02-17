<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\StageRequest;
use App\Models\Stage;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class StageCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class StageCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Stage::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/stage');
        CRUD::setEntityNameStrings('stage', 'stages');
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(StageRequest::class);
        CRUD::setEntityNameStrings('stage', 'stage');

        CRUD::field('settings')->type('stage_settings');
        //Обавить настрйоки
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    public function update(StageRequest $request)
    {
        $data = $request->validated();
        $settings = $data['settings'] ?? [];
        $stage = Stage::query()->with('settings')->find($data['id']);

        $save_settings = [];

        $stage->settings()->detach();
        foreach ($settings as $setting) {
            $value = $setting['field']['value'] ?? [];
            $values = is_array($value) ? $value : [$value];
            foreach ($values as $item_value) {
                $stage->settings()->attach($setting['id'], ['value' => $item_value]);
            }
        }
    }
}
