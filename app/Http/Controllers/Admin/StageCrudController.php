<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\StageRequest;
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

        CRUD::field('name');
        CRUD::field('color')->type('color');
        CRUD::field('pipeline_id');

        //Обавить настрйоки
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
