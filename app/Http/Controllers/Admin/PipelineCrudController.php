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
        CRUD::setEntityNameStrings(__('entity.add_pipeline'), __('entity.pipelines'));
        CRUD::denyAccess(['show']);
    }

    protected function setupListOperation()
    {
        CRUD::column('name')->label('Наименование');
    }

    protected function setupCreateOperation()
    {
        CRUD::setEntityNameStrings(__('entity.add_pipeline'), __('entity.pipeline'));
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
                    'type' => 'color',
                    'wrapper' => [
                        'class' => 'form-group col-md-1',
                    ],
                ],
                [
                    'name' => 'name',
                    'type' => 'text',
                    'wrapper' => [
                        'class' => 'form-group col-md-6',
                    ],
                ],
                [
                    'name' => 'url_setting',
                    'type' => 'custom_html',
                    'value' => '',
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
