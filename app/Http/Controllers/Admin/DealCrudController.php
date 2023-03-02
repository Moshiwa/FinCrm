<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\DealRequest;
use App\Models\Client;
use App\Models\Pipeline;
use App\Models\Stage;
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
    use \App\Http\Controllers\Admin\Operations\DealOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Deal::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/deal');
        CRUD::setEntityNameStrings('deal', 'deals');}

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('name');
        CRUD::column('pipeline_id');
        CRUD::column('stage_id');
        CRUD::column('created_at');
        CRUD::denyAccess(['update', 'delete', 'show', 'create']);

        $request_entity = $this->crud->getRequest()->get('client');
        if ($request_entity) {
            $this->crud->addClause('where', 'client_id', $request_entity);
        }


        $pipelines = Pipeline::query()->select('id', 'name')->get()->toArray();
        $pipelines = Arr::pluck($pipelines, 'name', 'id');
        CRUD::addFilter([
            'type'  => 'dropdown',
            'name'  => 'pipeline',
            'label' => 'Воронка'
        ], $pipelines, function($value) { // if the filter is active (the GET parameter "draft" exits)
            $this->crud->addClause('where', 'pipeline_id', $value);
        });

        $stages = Stage::query()->select('id', 'name')->get()->toArray();
        $stages = Arr::pluck($stages, 'name', 'id');
        CRUD::addFilter([
            'type'  => 'dropdown',
            'name'  => 'stage',
            'label' => 'Стадия'
        ], $stages, function($value) { // if the filter is active (the GET parameter "draft" exits)
            $this->crud->addClause('where', 'stage_id', $value);
        });
    }

    public function dealCreate()
    {
        $pipelines = Pipeline::query()->select('id', 'name')->get();
        $first_pipeline = $pipelines->first()->id;
        $stages = Stage::query()->where('pipeline_id', $first_pipeline)->get();;
        $first_stage = $stages->first()->id;

        $client_id = $this->crud->getCurrentEntryId();
        $client = Client::query()->find($client_id);
        $deal = $client->deals()->create([
            'name' => 'Новая сделка',
            'pipeline_id' => $first_pipeline,
            'stage_id' => $first_stage,
            'responsible_id' => backpack_user()->id,
        ]);

        return redirect('/admin/deal/' . $deal->id);
    }

}
