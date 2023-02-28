<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Models\Button;
use App\Models\Client;
use App\Models\Pipeline;
use App\Models\Stage;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Route;

trait DealOperation
{
    protected function setupDealRoutes($segment, $routeName, $controller)
    {
        Route::get($segment.'/{id}', [
            'as'        => $routeName.'.deal',
            'uses'      => $controller.'@getDealForm',
            'operation' => 'deal',
        ]);
    }


    public function newDealForm()
    {
        $pipelines = Pipeline::query()->select('id', 'name')->first();
        $first_pipeline = $pipelines->id;
        $stages = Stage::query()->where('pipeline_id', $first_pipeline)->first();
        $first_stage = $stages->id;

        $client_id = $this->crud->getCurrentEntryId();
        $client = Client::query()->find($client_id);
        $deal = $client->deals()->create([
            'name' => 'Новая сделка',
            'pipeline_id' => $first_pipeline,
            'stage_id' => $first_stage,
            'responsible_id' => backpack_user()->id,
        ]);

        $this->data['deal'] = $deal;
        $this->data['crud'] = $this->crud;
        $this->data['pipelines'] = $pipelines;
        $this->data['stages'] = $stages;

        return view('crud::create_deal', $this->data);
    }

    public function getDealForm()
    {
        $entry = $this->crud->getCurrentEntry();
        $this->crud->hasAccessOrFail('deal');
        $this->crud->setHeading('Сделка');

        $entry->load([
            'stage',
            'pipeline',
            'responsible',
            'client',
            'fields.type',
            'client.fields',
            'comments' => function ($query) {
                $query->offset(0)->limit(10)->orderBy('created_at', 'desc');
            },
            'comments.files',
            'comments.author' => function ($query) {
                $query->select('id', 'name');
            }
        ]);

        $this->data['crud'] = $this->crud;
        $this->data['entry'] = $entry;
        $this->data['pipelines'] = Pipeline::query()->select('id', 'name')->get();
        $this->data['stages'] = $entry->pipeline->stages;
        $this->data['buttons'] = Button::query()->with(['visible', 'action'])->where('pipeline_id', $entry->pipeline->id)->get();

        return view('crud::deal', $this->data);
    }

    protected function setupDealDefaults()
    {
        CRUD::allowAccess(['deal']);

        CRUD::operation('deal', function () {
            CRUD::loadDefaultOperationSettingsFromConfig();
        });

        CRUD::operation('list', function () {
            CRUD::addButton('line', 'deal', 'view', 'crud::buttons.deal');
        });
    }
}
