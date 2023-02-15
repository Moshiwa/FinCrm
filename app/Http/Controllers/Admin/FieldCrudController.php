<?php

namespace App\Http\Controllers\Admin;

use App\Enums\FieldsEntitiesEnum;
use App\Enums\FieldsEnum;
use App\Http\Requests\FieldRequest;
use App\Models\Field;
use App\Models\Pipeline;
use App\Services\SettingService;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Str;

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
        CRUD::column('type')
            ->label('Тип')
            ->type('closure')
            ->function(function($entry) {
                return __('fields.type.' . $entry->type);
            });
        CRUD::column('is_active')
            ->label('Активность')
            ->type('checkbox');

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

        $referer = $this->crud->getRequest()->headers->get('referer');
        $referer = parse_url($referer);
        $entity = FieldsEntitiesEnum::deal->value;
        CRUD::setEntityNameStrings('Поле', 'Поле сделок');
        if (Str::contains('entity=client', $referer['query'] ?? '')) {
            CRUD::setEntityNameStrings('Поле', 'Поле клиентов');
            $entity = FieldsEntitiesEnum::client->value;;
        }

        CRUD::field('type')
            ->label('Тип')
            ->type('select_from_array')
            ->options(
                SettingService::convertEnumToArray(FieldsEnum::cases())
            );
        CRUD::field('name')->label('Наименование');
        CRUD::field('entity')->type('hidden')->default($entity);
        CRUD::field('is_active')->label('Активирован');
        CRUD::field('options')->label('Варианты');
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
