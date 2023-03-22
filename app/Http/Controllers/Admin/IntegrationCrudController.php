<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\IntegrationRequest;
use App\Models\Integration;
use App\Models\User;
use App\Services\Sender\SenderService;
use App\Services\Telephony\Uiscom\UiscomService;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Http\Request;

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
        $this->data['user'] = backpack_user();
        $this->data['title'] = $this->crud->getTitle() ?? trans('backpack::crud.preview').' '.$this->crud->entity_name;

        return view('crud::detail_integration', $this->data);
    }

    public function getUisManagerId(Request $request)
    {
        $login = $request->get('login');
        $password = $request->get('password');

        if (empty($login) || empty($password)) {
            return response()->json([
                'success' => false,
            ]);
        }

        $id = (new UiscomService())->authManager($login, $password);

        if (empty($id)) {
            return response()->json([
                'success' => false,
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $id
        ]);
    }

    public function saveUiscom(Request $request)
    {
        $token = $request->get('token');
        $employee_id = $request->get('employee_id');
        $virtual_number = $request->get('virtual_number');

        $user = User::query()->find(backpack_user()->id);
        $user->update([
            'uiscom_token' => $token,
            'uiscom_employee_id' => $employee_id,
            'uiscom_virtual_number' => $virtual_number
        ]);
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
