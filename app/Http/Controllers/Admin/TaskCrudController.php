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

class TaskCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use TaskOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\Task::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/task');
        CRUD::setEntityNameStrings('task', 'tasks');
    }

    protected function setupListOperation()
    {
        $this->hiddenExecutorFilter();
        $this->hiddenManagerFilter();
        $this->hiddenResponsibleFilter();

        CRUD::addButton('top', 'task_create', 'view', 'crud::buttons.task_create');

        CRUD::column('name');
        CRUD::column('start');
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
            'comments' => function ($query) use ($comment_count) {
                $query->orderBy('created_at', 'desc')->offset(0)->limit($comment_count);
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
        $task->load([
            'comments' => function ($query) use ($offset) {
                $query->offset($offset)->limit(5)->orderBy('created_at', 'desc');
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
        $now = now();

        $task = Task::query()->create([
            'name' => 'Новая задача',
            'task_stage_id' => $first_stage,
            'description' => '',
            'start' => $now,
            'end' => null,
            'responsible_id' => backpack_user()->id,
            'manager_id' => null,
            'executor_id' => null,
        ]);

        return redirect('/admin/task/' . $task->id . '/detail');
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
