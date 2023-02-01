<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\StagesRequest;
use App\Models\Stage;
use App\Models\Status;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class StagesCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class StagesCrudController extends CrudController
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
        CRUD::setModel(Stage::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/stages');
        CRUD::setEntityNameStrings('stages', 'stages');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('color')
            ->wrapper([
                'style' => function($crud, $column, $entry, $related_key) {
                    return 'background:' . $entry->color;
                }
            ]);
        CRUD::column('name')
            ->label('Наименование');
        CRUD::column('status_id')
            ->label('Статус')
            ->entity('status')
            ->model('App\Models\Status');
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation([
            'name' => 'required|min:2',
        ]);

        CRUD::field('status_id')
            ->label('Статус')
            ->type('select')
            ->entity('status')
            ->model('App\Models\Status');
        CRUD::field('name')->label('Наименование');
        CRUD::field('color')
            ->label('Цвет')
            ->type('color');
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
