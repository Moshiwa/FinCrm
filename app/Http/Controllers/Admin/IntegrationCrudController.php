<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\IntegrationRequest;
use App\Models\Integration;
use App\Services\Sender\SenderService;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class IntegrationCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class IntegrationCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Integration::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/integration');
        CRUD::setEntityNameStrings(__('entity.crud_titles.action.integration'), __('entity.crud_titles.many.integration'));
    }

    protected function setupListOperation()
    {
        CRUD::column('name')->label('Наименование');
        CRUD::column('title')->label('Название');
        CRUD::column('access_token')->label('Токен');

    }

    public function show($id)
    {
        $this->crud->hasAccessOrFail('show');

        // get entry ID from Request (makes sure its the last ID for nested resources)
        $id = $this->crud->getCurrentEntryId() ?? $id;

        // get the info for that entry (include softDeleted items if the trait is used)
        if ($this->crud->get('show.softDeletes') && in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($this->crud->model))) {
            $this->data['entry'] = $this->crud->getModel()->withTrashed()->findOrFail($id);
        } else {
            $this->data['entry'] = $this->crud->getEntryWithLocale($id);
        }

        $this->data['crud'] = $this->crud;
        $this->data['integration'] = $this->crud->getCurrentEntry();
        $this->data['title'] = $this->crud->getTitle() ?? trans('backpack::crud.preview').' '.$this->crud->entity_name;

        return view('crud::detail_integration', $this->data);
    }

    public function save(IntegrationRequest $request)
    {
        $data = $request->validated();

        $integration = Integration::query()->find($data['id']);
        $service = SenderService::factory($integration->name);
        $integration->update([
            'login' => $data['login'],
            'password' => $data['password'],
            'access_token' => $data['access_token']
        ]);

        $response = $service->check();

        return response()->json([
            'success' => $response,
            'message' => $service->getError()
        ]);
    }
}
