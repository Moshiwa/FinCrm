<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Operations\TaskOperation;
use App\Http\Requests\TaskRequest;
use App\Models\Task;
use App\Models\TaskStage;
use App\Models\User;
use App\Services\Task\TaskService;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class TaskCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\Task::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/task');
        CRUD::setEntityNameStrings(__('entity.crud_titles.action.task'), __('entity.crud_titles.many.task'));
    }

    protected function setupListOperation()
    {
        $this->hiddenExecutorFilter();
        $this->hiddenManagerFilter();
        $this->hiddenResponsibleFilter();

        $stages = TaskStage::query()->get()->toArray();
        $stages = Arr::pluck($stages, 'name', 'id');
        CRUD::addFilter([
            'type'  => 'dropdown',
            'name'  => 'stage',
            'label' => 'Стадия'
        ], $stages, function($value) { // if the filter is active (the GET parameter "draft" exits)
            $this->crud->addClause('where', 'task_stage_id', $value);
        });

        $this->crud->addFilter([
            'name'  => 'deadline',
            'type'  => 'dropdown',
            'label' => 'Статус'
        ], [
            1 => 'Просрочена',
            2 => 'Не просрочена',
        ], function($value) {
            if ($value == 1) {
                $this->crud->addClause('where', 'deadline', '<', Carbon::now());
            } else {
                $this->crud->addClause('where', 'deadline', '>=', Carbon::now());
            }
        });

        CRUD::addButton('top', 'task_create', 'view', 'crud::buttons.task_create');

        CRUD::column('name')->label('Наименование');
        CRUD::column('stage')->label('Стадия');
        CRUD::column('responsible')->label('Ответственный');
        CRUD::column('manager')->label('Наблюдатель');
        CRUD::column('executor')->label('Исполнитель');
        CRUD::column('deadline')->label('Срок')->wrapper(['class' => 'test']);
        CRUD::column('overdue')
            ->label('')
            ->type('custom_html')
            ->value(function ($entry) {
                if ($entry->deadline < Carbon::now()) {
                    return '<a class="column-overdue" style="color: #d7556c;">Просрочена</a>';
                }
                return '<a class="column-not-overdue" style="color: #04AA6D;">Не просрочена</a>';
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
        $this->data['task'] = $this->data['entry'];
        $this->data['title'] = $this->crud->getTitle() ?? trans('backpack::crud.preview').' '.$this->crud->entity_name;

        // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
        return view('crud::detail_task', $this->data);
    }

    private function hiddenExecutorFilter()
    {
        $request_entity = $this->crud->getRequest()->get('executor');
        if ($request_entity) {
            $this->crud->addClause('where', 'executor_id', $request_entity);
        }
    }

    private function hiddenManagerFilter()
    {
        $request_entity = $this->crud->getRequest()->get('manager');
        if ($request_entity) {
            $this->crud->addClause('where', 'manager_id', $request_entity);
        }
    }

    private function hiddenResponsibleFilter()
    {
        $request_entity = $this->crud->getRequest()->get('responsible');
        if ($request_entity) {
            $this->crud->addClause('where', 'responsible_id', $request_entity);
        }
    }

    public function update(TaskRequest $request)
    {
        $service = new TaskService();
        $data = $request->validated();

        $task = Task::query()->find($data['id']);
        $comment_data = $service->prepareCommentData($task, $data);
        $task = $service->updateTask($task, $data);
        $service->createNewMessage($task, $comment_data);
        $task->fields()->sync($data['fields'] ?? []);
        $service->updateComments($task, $data);

        $comment_count = $data['comment_count'] ?? 10;
        $type = $request->get('type');
        $sort = $request->get('date_sort', 'desc');

        $task->load([
            'stage',
            'stage.buttons.visible',
            'stage.buttons.action',
            'responsible'=> function ($query) {
                $query->select('id', 'name');
            },
            'manager'=> function ($query) {
                $query->select('id', 'name');
            },
            'executor'=> function ($query) {
                $query->select('id', 'name');
            },
            'fields',
            'comments' => function ($query) use ($type, $sort, $comment_count) {
                $query->when($type, function ($query, $type) {
                    $query->where('type', $type);
                })->offset(0)->limit($comment_count)->orderBy('created_at', $sort);
            },
            'comments.files',
            'comments.author' => function ($query) {
                $query->select('id', 'name');
            }
        ]);

        return response()->json([
            'task' => $task,
            'stages' => TaskStage::query()->get(),
            'users' => User::query()->select('id', 'name')->get(),
        ]);
    }

    public function loadComments(Task $task, Request $request)
    {
        $offset = $request->get('offset');
        $type = $request->get('type');
        $sort = $request->get('date_sort', 'desc');
        $task->load([
            'comments' => function ($query) use ($offset, $type, $sort) {
                $query
                    ->when($type, function ($query, $type) {
                        $query->where('type', $type);
                    })
                    ->offset($offset)
                    ->limit(5)
                    ->orderBy('created_at', $sort);
            },
            'comments.files',
            'comments.author' => function ($query) {
                $query->select('id', 'name');
            }
        ]);

        return $task;
    }

    public function taskCreate()
    {
        $stage = TaskStage::query()->first();
        $first_stage = $stage->id;

        $deadline = time() + $stage->calculated_deadline;

        $task = Task::query()->create([
            'name' => 'Новая задача',
            'task_stage_id' => $first_stage,
            'description' => '',
            'deadline' => Carbon::createFromTimestamp($deadline),
            'responsible_id' => backpack_user()->id,
            'manager_id' => null,
            'executor_id' => null,
        ]);

        return redirect('/admin/task/' . $task->id . '/show');
    }

    public function delete(Task $task, Request $request)
    {
        if (backpack_user()->can('tasks.delete')) {
            $task->delete();

            return response()->json([
                'success' => true,
                'errors' => [],
            ]);
        }

        return response()->json([
            'success' => false,
            'errors' => [
                'У вас недостаточно прав'
            ],
        ], 403);
    }
}
