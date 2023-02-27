<?php

namespace App\Http\Controllers\Admin;

use App\Enums\FieldsEntitiesEnum;
use App\Http\Requests\FieldRequest;
use App\Models\Field;
use App\Models\FieldType;
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

    public function setup()
    {
        CRUD::setModel(Field::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/field');
        CRUD::setEntityNameStrings('field', 'field');
        CRUD::denyAccess(['show']);
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

        CRUD::addButton('top', 'pipelines', 'view', 'crud::buttons.pipelines');

        $request_entity = $this->crud->getRequest()->get('entity');
        if ($request_entity === FieldsEntitiesEnum::deal->value) {
            $this->crud->addClause('where', 'entity', FieldsEntitiesEnum::deal->value);
            CRUD::setEntityNameStrings('Поле', 'Поля сделок');
        } elseif ($request_entity === FieldsEntitiesEnum::client->value) {
            $this->crud->addClause('where', 'entity', FieldsEntitiesEnum::client->value);
            CRUD::setEntityNameStrings('Поле', 'Поля клиентов');
        } else {
            $this->crud->addClause('where', 'entity', FieldsEntitiesEnum::deal->value);
            CRUD::setEntityNameStrings('Поле', 'Поля сделок');
        }
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(FieldRequest::class);
        Widget::add()->type('script')->content(asset('assets/js/admin/fields/toggle_options_block.js'));

        $referer = $this->crud->getRequest()->headers->get('referer');
        $referer = parse_url($referer);
        $entity = FieldsEntitiesEnum::deal->value;
        CRUD::setEntityNameStrings('Поле', 'Поле сделок');
        if (Str::contains('entity=client', $referer['query'] ?? '')) {
            CRUD::setEntityNameStrings('Поле', 'Поле клиентов');
            $entity = FieldsEntitiesEnum::client->value;;
        }


        CRUD::field('type_id')
            ->label('Тип')
            ->entity('type')
            ->attribute('tr_name')
            ->model(FieldType::class)
            ->type('select');
        CRUD::field('name')->label('Наименование');
        CRUD::field('entity')->type('hidden')->default($entity);
        CRUD::field('is_active')->label('Активирован');
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

    public function toggleActivity(Request $request, Field $field)
    {
        $is_active = $request->get('is_active');
        $field->update(['is_active' => $is_active]);
    }
}
