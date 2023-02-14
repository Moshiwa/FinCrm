<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ClientRequest;
use App\Models\Client;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * Class ClientCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ClientCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Client::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/client');
        CRUD::setEntityNameStrings('client', 'clients');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('name');
        CRUD::column('created_at');
        CRUD::column('updated_at');

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(ClientRequest::class);
        CRUD::field('client')->type('client');

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number']));
         */
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

    public function update(ClientRequest $request)
    {
        $data = $request->validated();
        $fields = $data['fields'];
        $client = Client::query()->with('fields')->find($data['id']);
        $client->update([
            'name' => $data['name']
        ]);

        $save_fields = [];
        foreach ($fields as $field) {
            if (empty($field['id']) || empty($field['value'])) {
                continue;
            }

            $save_fields[$field['id']] = ['value' => $field['value']];
        }
        $client->fields()->sync($save_fields);
        return $this->crud->performSaveAction($data['id']);
    }

    public function store(ClientRequest $request)
    {
        $data = $request->validated();
        $fields = $data['fields'];

        $client = new Client();
        $client = $client->create([
            'name' => $data['name']
        ]);


        $save_fields = [];
        foreach ($fields as $field) {
            if (empty($field['id']) || empty($field['value'])) {
                continue;
            }

            $save_fields[$field['id']] = ['value' => $field['value']];
        }


        $client->fields()->sync($save_fields);
        return $this->crud->performSaveAction($client->id);
    }

    /*public function save(ClientRequest $request)
    {
        $data = $request->validated();
        $client = Client::query()->with('fields')->find($data['id']);
        $client->update([
            'name' => $data['name']
        ]);
        $client->fields()->sync($data['fields'] ?? []);
    }*/
}
