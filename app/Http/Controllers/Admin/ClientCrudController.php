<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ClientRequest;
use App\Models\Client;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
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
    use \App\Http\Controllers\Admin\Operations\DealCreateOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\Client::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/client');
        CRUD::setEntityNameStrings('client', 'clients');
        CRUD::denyAccess(['show']);
    }

    protected function setupListOperation()
    {
        CRUD::column('name');
        CRUD::column('Количество сделок')
            ->type('custom_html')
            ->value(function ($entry) {
                return '<a href="/admin/deal?client='.$entry->id.'">' . $entry->deals->count() . '</a>';
            });
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(ClientRequest::class);
        CRUD::field('client')->type('client');
    }

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
}
