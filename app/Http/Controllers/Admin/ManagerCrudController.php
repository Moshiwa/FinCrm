<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Operations\ManagerActionsInfoOperation;
use App\Http\Requests\ManagerRequest;
use App\Models\User;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Http\Request;

class ManagerCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use ManagerActionsInfoOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\User::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/manager');
        CRUD::setEntityNameStrings('manager', 'managers');
        CRUD::denyAccess(['show', 'delete', 'update']);
    }

    protected function setupListOperation()
    {
        CRUD::addButton('line', 'manager-actions', 'view', 'crud::buttons.manager_actions');

        CRUD::column('name');
        CRUD::column('email');
    }

    public function loadComments(User $user, Request $request)
    {
        $offset = $request->get('offset');
        $user->load([
            'comments' => function ($query) use ($offset) {
                $query->offset($offset)->limit(5)->orderBy('created_at', 'desc');
            },
            'comments.files',
        ]);

        $user = $user->toArray();

        return $user;
    }
}
