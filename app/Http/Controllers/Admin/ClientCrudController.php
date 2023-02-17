<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ClientRequest;
use App\Models\Client;
use App\Services\Client\ClientService;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

class ClientCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \App\Http\Controllers\Admin\Operations\DealCreateOperation;

    private ClientService $service;

    public function setup()
    {
        CRUD::setModel(\App\Models\Client::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/client');
        CRUD::setEntityNameStrings('client', 'clients');
        CRUD::denyAccess(['show']);

        $this->service = new ClientService();
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

        $fields = $data['fields'] ?? [];

        $client = Client::with('fields')->find($data['id']);
        $client = $this->service->updateOrCreateClient($client, $data);
        $client = $this->service->updateFields($client, $fields);

        return $this->crud->performSaveAction($client->id);
    }

    public function store(ClientRequest $request)
    {
        $data = $request->validated();

        $fields = $data['fields'] ?? [];
dd($data);
        $client = new Client();
        $client = $this->service->updateOrCreateClient($client, $data);
        $client = $this->service->updateFields($client, $fields);

        return $this->crud->performSaveAction($client->id);
    }
}
