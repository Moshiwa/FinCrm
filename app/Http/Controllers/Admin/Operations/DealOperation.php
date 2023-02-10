<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Models\Field;
use App\Models\Pipeline;
use App\Models\Stage;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Route;

trait DealOperation
{
    /**
     * Define which routes are needed for this operation.
     *
     * @param string $segment    Name of the current entity (singular). Used as first URL segment.
     * @param string $routeName  Prefix of the route name.
     * @param string $controller Name of the current CrudController.
     */
    protected function setupDealRoutes($segment, $routeName, $controller)
    {
        Route::get($segment.'/{id}/deal', [
            'as'        => $routeName.'.deal',
            'uses'      => $controller.'@getDealForm',
            'operation' => 'deal',
        ]);
    }


    public function getDealForm()
    {
        $entry = $this->crud->getCurrentEntry();
        $this->crud->hasAccessOrFail('deal');
        $this->crud->setHeading('Сделка');

        $comments = $entry->comments()->orderBy('created_at', 'desc')->with(['author', 'files'])->paginate(5);

        $entry->load([
            'stage',
            'stage.settings',
            'pipeline',
            'responsible',
            'client',
            'fields',
            'client.fields',
            'comments' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
            'comments.files',
            'comments.author' => function ($query) {
                $query->select('id', 'name');
            }
        ]);

        $this->data['crud'] = $this->crud;
        $this->data['entry'] = $entry;
        $this->data['comments'] = $comments;
        $this->data['pipelines'] = Pipeline::query()->select('id', 'name')->get();
        $this->data['stages'] = Stage::query()->where('pipeline_id', $entry->pipeline->id)->get();
        $this->data['fields'] = Field::query()->get();

        return view('crud::deal', $this->data);
    }

    /**
     * Add the default settings, buttons, etc that this operation needs.
     */
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
