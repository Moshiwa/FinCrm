<?php

namespace App\Http\Controllers\Admin;

use App\Enums\FieldsEntitiesEnum;
use App\Http\Requests\FieldRequest;
use App\Models\Field;
use App\Models\FieldType;
use App\Services\Dadata\DadataService;
use App\Services\Field\FieldService;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FieldCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ReorderOperation;

    public function setup()
    {
        CRUD::setModel(Field::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/field');
        CRUD::setEntityNameStrings(__('entity.crud_titles.action.field'), __('entity.crud_titles.many.field'));

        CRUD::denyAccess(['show']);
        if (! backpack_user()->can('fields.create')) {
            CRUD::denyAccess(['create']);
        }

        if (! backpack_user()->can('fields.update')) {
            CRUD::denyAccess(['update']);
        }

        if (! backpack_user()->can('fields.delete')) {
            CRUD::denyAccess(['delete']);
        }

        if (! backpack_user()->can('fields.list')) {
            CRUD::denyAccess(['list']);
        }
    }

    protected function setupListOperation()
    {
        CRUD::column('name')->label('Название');
        CRUD::column('type_id')
            ->type('relationship')
            ->entity('type')
            ->model(FieldType::class)
            ->attribute('tr_name')
            ->label('Тип');
        CRUD::column('активность')
            ->type('custom_html')
            ->value(function ($entry) {
                return view('crud::buttons.field_checkbox', ['entry' => $entry]);
            });

        CRUD::addButton('top', 'pipelines', 'view', 'crud::buttons.field_entities');

        $entity = FieldService::getEntityFromRequest($this->crud->getRequest());
        $this->crud->addClause('where', 'entity', $entity->value);
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(FieldRequest::class);
        Widget::add()->type('script')->content(asset('assets/js/admin/fields/toggle_options_block.js'));

        $entity = FieldService::getEntityFromRequest($this->crud->getRequest());

        CRUD::field('type_id')
            ->label('Тип')
            ->entity('type')
            ->attribute('tr_name')
            ->model(FieldType::class)
            ->type('select');
        CRUD::field('name')->label('Наименование');
        CRUD::field('entity')->type('hidden')->default($entity);
        CRUD::field('is_active')->label('Активирован');

        CRUD::field('is_required')->label('Обязательность');
        CRUD::field('options')
            ->type('table')
            ->columns(['value' => 'Значение'])
            ->entity_singular('Вариант')
            ->label('Варианты');
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    public function findAddress(Request $request)
    {
        $search = $request->get('search');
        if (strlen($search) >= 3) {
            $dadata = new DadataService();
            $dadata->setCount(20);
            $addresses = $dadata->findAddress($search);
            return response()->json([
                'data' => $addresses,
                'errors' => [],
            ]);
        }

        return response()->json([
            'data' => [],
            'errors' => ['Должно быть больше 3х символов'],
        ]);
    }

    public function toggleActivity(Request $request, Field $field)
    {
        if (backpack_user()->can('fields.update')) {
            $is_active = $request->get('is_active');
            $field->update(['is_active' => $is_active]);
        }
    }

    protected function setupReorderOperation()
    {
        $this->crud->set('reorder.label', 'name');
        $this->crud->set('reorder.max_level', 1);
    }
}
