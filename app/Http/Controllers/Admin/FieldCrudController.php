<?php

namespace App\Http\Controllers\Admin;

use App\Enums\FieldsEnum;
use App\Http\Requests\FieldRequest;
use App\Models\Field;
use App\Services\SettingService;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class FieldsCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class FieldCrudController extends CrudController
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

        CRUD::setModel(Field::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/field');
        CRUD::setEntityNameStrings('field', 'field');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('name')->label('Название');
        CRUD::column('type')
            ->label('Тип')
            ->type('closure')
            ->function(function($entry) {
                return __('fields.type.' . $entry->type);
            });

        CRUD::column('entity')
            ->label('Сущность')
            ->type('closure')
            ->function(function($entry) {
                return __('fields.type.' . $entry->entity);
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
        CRUD::setValidation(FieldRequest::class);

        CRUD::field('type')
            ->label('Тип')
            ->type('select_from_array')
            ->options(
                SettingService::convertEnumToArray(FieldsEnum::cases())
            );
        CRUD::field('name')->label('Наименование');
        CRUD::field('entity')
            ->label('Сущность')
            ->type('select_from_array')
            ->options(['client' => 'Кленты', 'deal' => 'Сделка'])
        ;
        CRUD::field('is_active')->label('Активирован');
        CRUD::field('options')->label('Варианты');
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
