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
        $offset_deal = $request->get('offset_deal');
        $offset_task = $request->get('offset_task');
        $user->load([
            'deal_comments' => function ($query) use ($offset_deal) {
                $query->offset($offset_deal)->limit(5)->orderBy('created_at', 'desc');
            },
            'deal_comments.files',
            'task_comments' => function ($query) use ($offset_task) {
                $query->offset($offset_task)->limit(5)->orderBy('created_at', 'desc');
            },
            'task_comments.files'
        ]);

        $user = $user->toArray();

        return $user;
    }
}
