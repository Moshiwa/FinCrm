<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Models\Client;
use App\Models\Pipeline;
use App\Models\Stage;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Route;

trait DealCreateOperation
{
    protected function setupDealCreateRoutes($segment, $routeName, $controller)
    {
        Route::get($segment.'/{id}/deal-create', [
            'as'        => $routeName.'.dealCreate',
            'uses'      => $controller.'@dealCreate',
            'operation' => 'dealCreate',
        ]);
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

    protected function setupDealCreateDefaults()
    {
        CRUD::allowAccess(['dealCreate']);

        CRUD::operation('dealCreate', function () {
            CRUD::loadDefaultOperationSettingsFromConfig();
        });

        CRUD::operation('list', function () {
            CRUD::addButton('line', 'deal-create', 'view', 'crud::buttons.deal_create');
        });
    }
}
