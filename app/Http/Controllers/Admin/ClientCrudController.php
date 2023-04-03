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

    private ClientService $service;

    public function setup()
    {
        CRUD::setModel(\App\Models\Client::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/client');
        CRUD::setEntityNameStrings(__('entity.crud_titles.action.client'), __('entity.crud_titles.many.client'));
        CRUD::denyAccess(['show']);
        if (! backpack_user()->can('clients.create')) {
            CRUD::denyAccess(['create']);
        }

        if (! backpack_user()->can('clients.update')) {
            CRUD::denyAccess(['update']);
        }

        if (! backpack_user()->can('clients.delete')) {
            CRUD::denyAccess(['delete']);
        }

        if (! backpack_user()->can('clients.list')) {
            CRUD::denyAccess(['list']);
        }

        $this->service = new ClientService();
    }

    protected function setupListOperation()
    {
        CRUD::addButton('line', 'deal-create', 'view', 'crud::buttons.deal_create');

        CRUD::column('name')->label('Имя');
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

        $client = new Client();
        $client = $this->service->updateOrCreateClient($client, $data);
        $client = $this->service->updateFields($client, $fields);

        return $this->crud->performSaveAction($client->id);
    }

    public function destroy($id)
    {
        $this->crud->hasAccessOrFail('delete');

        // get entry ID from Request (makes sure its the last ID for nested resources)
        $id = $this->crud->getCurrentEntryId() ?? $id;

        try {
            $this->crud->delete($id);
        } catch (\Exception $e) {
            $code = $e->getCode();
            if ($code == 23503) {
                return \Alert::add('error', 'Вы не можете удалить клиента у которого имееются сделки')->flash();
            }

            return \Alert::add('error', 'Произошла ошибка')->flash();
        }

        return true;
    }
}
