<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PipelineRequest;
use App\Services\Stage\StageService;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class PipelineCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\Pipeline::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/pipeline');
        CRUD::setEntityNameStrings(__('entity.crud_titles.action.pipeline'), __('entity.crud_titles.many.pipeline'));
        CRUD::denyAccess(['show']);
        if (! backpack_user()->can('pipelines.update')) {
            CRUD::denyAccess(['update']);
        }

        if (! backpack_user()->can('pipelines.delete')) {
            CRUD::denyAccess(['delete']);
        }

        if (! backpack_user()->can('pipelines.list')) {
            CRUD::denyAccess(['list']);
        }
    }

    protected function setupListOperation()
    {
        CRUD::column('name')->label('Наименование');
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(PipelineRequest::class);

        CRUD::field('name')->label('Наименование');
        CRUD::field('stages')
            ->label('Стадии')
            ->type('relationship')
            ->default(StageService::getDefaultStages())
            ->min_rows(1)
            ->subfields([
                [
                    'name' => 'color',
                    'label' => 'Цвет',
                    'type' => 'color',
                    'wrapper' => [
                        'class' => 'form-group col-md-1',
                    ],
                ],
                [
                    'name' => 'name',
                    'label' => 'Наименование',
                    'type' => 'text',
                    'wrapper' => [
                        'class' => 'form-group col-md-6',
                    ],
                ],
            ]
        );
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
