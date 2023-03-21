<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Http\Request;

class UserCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\User::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/user');
        CRUD::setEntityNameStrings(__('entity.crud_titles.action.user'), __('entity.crud_titles.many.user'));
    }

    protected function setupListOperation()
    {
        CRUD::column('name');
        CRUD::column('email');
        CRUD::column('password');
    }

    protected function setupCreateOperation()
    {
        CRUD::field('name');
        CRUD::field('email');
        CRUD::field('password');
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    public function findUsers(Request $request)
    {
        $search = $request->get('search');
        if (strlen($search) >= 3) {
            $users = User::query()->where('name', 'like', $search . '%')->get();

            return response()->json([
                'data' => $users,
                'errors' => [],
            ]);
        }

        return response()->json([
            'data' => [],
            'errors' => ['Должно быть больше 3х символов'],
        ]);
    }
}
