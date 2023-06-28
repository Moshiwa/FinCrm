<?php

namespace App\Http\Controllers\Admin\PermissionManager;

use App\Helpers\RequestHelper;
use App\Models\User;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\Widget;
use Backpack\PermissionManager\app\Http\Requests\UserStoreCrudRequest as StoreRequest;
use Backpack\PermissionManager\app\Http\Requests\UserUpdateCrudRequest as UpdateRequest;
use Illuminate\Support\Facades\Hash;

class UserCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation {
        store as traitStore;
    }
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation {
        update as traitUpdate;
    }
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;

    public function setup()
    {
        $this->crud->setModel(config('backpack.permissionmanager.models.user'));
        $this->crud->setEntityNameStrings(trans('backpack::permissionmanager.user'), trans('backpack::permissionmanager.users'));
        $this->crud->setRoute(backpack_url('user'));

        if (!backpack_user()->hasRole('admin')) {
            $this->crud->denyAccess(['list', 'update', 'delete', 'show']);
        }
    }

    public function setupListOperation()
    {
        $this->crud->addColumns([
            [
                'name' => 'name',
                'label' => trans('backpack::permissionmanager.name'),
                'type' => 'text',
            ],
            [
                'name' => 'email',
                'label' => trans('backpack::permissionmanager.email'),
                'type' => 'email',
            ],
            [ // n-n relationship (with pivot table)
                'label' => trans('backpack::permissionmanager.roles'), // Table column heading
                'type' => 'select_multiple',
                'name' => 'roles', // the method that defines the relationship in your Model
                'entity' => 'roles', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => config('permission.models.role'), // foreign key model
            ],
            [ // n-n relationship (with pivot table)
                'label' => trans('backpack::permissionmanager.extra_permissions'), // Table column heading
                'type' => 'select_multiple',
                'name' => 'permissions', // the method that defines the relationship in your Model
                'entity' => 'permissions', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => config('permission.models.permission'), // foreign key model
            ],
        ]);

        if (backpack_pro()) {
            // Role Filter
            $this->crud->addFilter(
                [
                    'name' => 'role',
                    'type' => 'dropdown',
                    'label' => trans('backpack::permissionmanager.role'),
                ],
                config('permission.models.role')::all()->pluck('name', 'id')->toArray(),
                function ($value) { // if the filter is active
                    $this->crud->addClause('whereHas', 'roles', function ($query) use ($value) {
                        $query->where('role_id', '=', $value);
                    });
                }
            );

            // Extra Permission Filter
            $this->crud->addFilter(
                [
                    'name' => 'permissions',
                    'type' => 'select2',
                    'label' => trans('backpack::permissionmanager.extra_permissions'),
                ],
                config('permission.models.permission')::all()->pluck('name', 'id')->toArray(),
                function ($value) { // if the filter is active
                    $this->crud->addClause('whereHas', 'permissions', function ($query) use ($value) {
                        $query->where('permission_id', '=', $value);
                    });
                }
            );
        }
    }

    public function setupCreateOperation()
    {
        $this->addUserFields();
        $this->crud->setValidation(StoreRequest::class);
    }

    public function setupUpdateOperation()
    {
        $this->addUserFields();
        if (backpack_user()->hasRole('admin')) {
            $this->addTelephonyFields();
            $this->crud->addField([
                'name' => 'token',
                'type' => 'user_token',
                'tab' => 'API',
                'wrapper' => [
                    'class' => 'form-group col-md-12'
                ]
            ]);
        }
        $this->crud->setValidation(UpdateRequest::class);
    }

    /**
     * Store a newly created resource in the database.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        $this->crud->setRequest($this->crud->validateRequest());
        $this->crud->setRequest($this->handlePasswordInput($this->crud->getRequest()));
        $this->crud->unsetValidation(); // validation has already been run

        return $this->traitStore();
    }

    /**
     * Update the specified resource in the database.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update()
    {
        $this->crud->setRequest(RequestHelper::updateRequestRoles($this->crud->validateRequest()));
        $this->crud->setRequest($this->handlePasswordInput($this->crud->getRequest()));
        $this->crud->unsetValidation(); // validation has already been run

        return $this->traitUpdate();
    }

    /**
     * Handle password input fields.
     */
    protected function handlePasswordInput($request)
    {
        // Remove fields not present on the user.
        $request->request->remove('password_confirmation');
        $request->request->remove('roles_show');
        $request->request->remove('permissions_show');

        // Encrypt password if specified.
        if ($request->input('password')) {
            $request->request->set('password', Hash::make($request->input('password')));
        } else {
            $request->request->remove('password');
        }

        return $request;
    }

    protected function addUserFields()
    {
        $this->crud->addFields([
            [
                'name' => 'name',
                'label' => trans('backpack::permissionmanager.name'),
                'type' => 'text',
                'tab' => 'Основное'
            ],
            [
                'name' => 'email',
                'label' => trans('backpack::permissionmanager.email'),
                'type' => 'email',
                'tab' => 'Основное'
            ],
            [
                'name' => 'password',
                'label' => trans('backpack::permissionmanager.password'),
                'type' => 'password',
                'tab' => 'Основное'
            ],
            [
                'name' => 'password_confirmation',
                'label' => trans('backpack::permissionmanager.password_confirmation'),
                'type' => 'password',
                'tab' => 'Основное'
            ],
            [
                'name' => 'spaces',
                'label' => 'Доступ к организациям',
                'tab' => 'Права'
            ],
            [
                // two interconnected entities
                'label' => trans('backpack::permissionmanager.user_role_permission'),
                'field_unique_name' => 'user_role_permission',
                'type' => 'checklist_dependency',
                'name' => ['roles', 'permissions'],
                'tab' => 'Права',
                'subfields' => [
                    'primary' => [
                        'label' => trans('backpack::permissionmanager.roles'),
                        'name' => 'roles', // the method that defines the relationship in your Model
                        'entity' => 'roles', // the method that defines the relationship in your Model
                        'entity_secondary' => 'permissions', // the method that defines the relationship in your Model
                        'attribute' => 'name', // foreign key attribute that is shown to user
                        'model' => config('permission.models.role'), // foreign key model
                        'pivot' => true, // on create&update, do you need to add/delete pivot table entries?]
                        'number_columns' => 3, //can be 1,2,3,4,6
                    ],
                    'secondary' => [
                        'label' => mb_ucfirst(trans('backpack::permissionmanager.permission_plural')),
                        'name' => 'permissions', // the method that defines the relationship in your Model
                        'entity' => 'permissions', // the method that defines the relationship in your Model
                        'entity_primary' => 'roles', // the method that defines the relationship in your Model
                        'attribute' => 'display_name', // foreign key attribute that is shown to user
                        'model' => config('permission.models.permission'), // foreign key model
                        'pivot' => true, // on create&update, do you need to add/delete pivot table entries?]
                        'number_columns' => 2, //can be 1,2,3,4,6
                    ],
                ],
            ],
        ]);
    }

    protected function addTelephonyFields()
    {
        $this->crud->addFields([
            [
                'name' => 'uiscom_token',
                'label' => 'Токен',
                'tab' => 'Телефония',
                'wrapper' => [
                    'class' => 'form-group col-md-12'
                ]
            ],
            [
                'name' => 'uiscom_virtual_number',
                'label' => 'Виртуальный номер',
                'tab' => 'Телефония',
                'wrapper' => [
                    'class' => 'form-group col-md-6'
                ]
            ],
            [
                'name' => 'uiscom_employee_id',
                'label' => 'Идентификатор менеджера',
                'tab' => 'Телефония',
                'wrapper' => [
                    'class' => 'form-group col-md-6'
                ]
            ],
            [
                'name' => 'separator_first',
                'type' => 'custom_html',
                'tab' => 'Телефония',
                'value' => '<hr>'
            ],
            [
                'name' => 'user_telephony_auth',
                'type' => 'user_telephony_auth',
                'tab' => 'Телефония',
                'wrapper' => [
                    'class' => 'form-group col-md-12'
                ]
            ],
            [
                'name' => 'separator_second',
                'type' => 'custom_html',
                'tab' => 'Телефония',
                'value' => '<hr>'
            ],
            [
                'name' => 'helper',
                'type' => 'custom_html',
                'tab' => 'Телефония',
                'value' => '<div>
                            1. Как получить токен
                            <br>
                            <img style="width: 450px" src="/assets/images/info/uiscom_get_token.png"/>
                            <br>
                            2. Альтернативный способ получения Идентификатора пользователя
                            <br>
                            <img style="width: 450px" src="/assets/images/info/uiscom_get_id.png"/>
                            <br>
                        </div>
            '
            ]
        ]);
    }

    public function generateToken(User $user)
    {
        if (backpack_user()->hasRole('admin')) {
            $user->tokens()->delete();
            $token = $user->createToken('api');

            return response()->json([
                'success' => true,
                'token' => $token->plainTextToken
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'У вас недостаточно прав'
        ], 403);
    }
}
