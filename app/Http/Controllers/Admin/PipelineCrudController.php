<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PipelineRequest;
use App\Models\Status;
use App\Services\Space\SpaceService;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class PipelinesCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PipelineCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Pipeline::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/pipeline');
        CRUD::setEntityNameStrings('pipeline', 'pipeline');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('name')->label('Наименование');
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(PipelineRequest::class);

        $status_process = Status::query()->select('id')->where('name', 'process')->first();
        $status_done = Status::query()->select('id')->where('name', 'done')->first();
        $status_cancel = Status::query()->select('id')->where('name', 'cancel')->first();
        $stages = [
            [
                'name' => 'В работе',
                'status_id' => $status_process->id,
                'color' => '#0050FF'
            ],
            [
                'name' => 'Выполнено',
                'status_id' => $status_done->id,
                'color' => '#28FC2A'
            ],
            [
                'name' => 'Отменено',
                'status_id' => $status_cancel->id,
                'color' => '#FE3F6D'
            ],

        ];

        CRUD::field('name')->label('Наименование');
        CRUD::field('stages')->label('Стадии')->type('relationship')->default($stages)->min_rows(1)->subfields([
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
                'name' => 'status_id',
                'type' => 'select',
                'entity' => 'status',
                'model' => 'App\Models\Status',
                'wrapper' => [
                    'class' => 'form-group col-md-4',
                ],
            ],
        ]);
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
