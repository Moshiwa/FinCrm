<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Operations\ManagerActionsInfoOperation;
use App\Http\Requests\ManagerRequest;
use App\Models\User;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ManagerCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\User::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/manager');
        CRUD::setEntityNameStrings('manager', 'managers');
    }

    protected function setupListOperation()
    {
        CRUD::removeButton('show');
        CRUD::addButton('line', 'manager-actions', 'view', 'crud::buttons.manager_actions');


        CRUD::column('name');
        CRUD::column('email');
        CRUD::column('Сделки')
            ->type('custom_html')
            ->value(function ($entry) {
                return '<a href="/admin/deal?responsible='.$entry->id.'">' . $entry->deals->count() . '</a>';
            });

        CRUD::column('Выполняемые задачи')
            ->type('custom_html')
            ->value(function ($entry) {
                return '<a href="/admin/task?executor='.$entry->id.'">' . $entry->execute_tasks->count() . '</a>';
            });

        CRUD::column('Отслеживаемые задач')
            ->type('custom_html')
            ->value(function ($entry) {
                return '<a href="/admin/task?manager='.$entry->id.'">' . $entry->manage_tasks->count() . '</a>';
            });

        CRUD::column('Поставленные задач')
            ->type('custom_html')
            ->value(function ($entry) {
                return '<a href="/admin/task?responsible='.$entry->id.'">' . $entry->responsible_tasks->count() . '</a>';
            });
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
        $this->data['user'] = $this->data['entry'];
        $this->data['title'] = $this->crud->getTitle() ?? trans('backpack::crud.preview').' '.$this->crud->entity_name;

        // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
        return view('crud::detail_manager', $this->data);
    }

    public function loadComments(User $user, Request $request)
    {
        $offset = $request->get('offset');
        $type = $request->get('type');
        $sort = $request->get('date_sort', 'desc');
        $start = $request->get('start');
        $end = $request->get('end');

        $user->load([
            'comments' => function ($query) use ($offset, $type, $sort, $start, $end) {
                $query
                    ->when($type, function ($query, $type) {
                        $query->where('type', $type);
                    })
                    ->when($start, function ($query, $start) {
                        $query->where('created_at', '>=', Carbon::createFromTimestamp($start));
                    })
                    ->when($end, function ($query, $end) {
                        $query->where('created_at', '<=', Carbon::createFromTimestamp($end));
                    })
                    ->offset($offset)
                    ->limit(5)
                    ->orderBy('created_at', $sort);
            },
            'comments.files',
        ]);

        $user = $user->toArray();

        return $user;
    }
}
