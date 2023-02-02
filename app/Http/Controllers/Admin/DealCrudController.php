<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\DealRequest;
use App\Models\Pipeline;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Arr;

/**
 * Class DealCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class DealCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
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
        CRUD::setModel(\App\Models\Deal::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/deal');
        CRUD::setEntityNameStrings('deal', 'deals');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('name');
        CRUD::column('comment');
        CRUD::column('pipeline_id');
        CRUD::column('created_at');
        CRUD::column('updated_at');

        $pipelines = Pipeline::query()->select('id', 'name')->get()->toArray();
        $pipelines = Arr::pluck($pipelines, 'name', 'id');
        CRUD::addFilter([
            'type'  => 'dropdown',
            'name'  => 'pipeline',
            'label' => 'Воронка'
        ], $pipelines, function($value) { // if the filter is active (the GET parameter "draft" exits)
            $this->crud->addClause('where', 'pipeline_id', $value);
        });
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(DealRequest::class);

        CRUD::field('name');
        CRUD::field('comment');
        CRUD::field('pipeline_id')
            ->type('select')
            ->entity('pipeline')
            ->model('App\Models\Pipeline');

        CRUD::field('stage_id')
            ->type('select')
            ->entity('stage')
            ->model('App\Models\Stage');


        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number']));
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
