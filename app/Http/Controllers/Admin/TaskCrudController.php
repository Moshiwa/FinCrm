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
        CRUD::addButton('top', 'task_create', 'view', 'crud::buttons.task_create');

        CRUD::column('name');
        CRUD::column('start');
    }

    public function update(TaskRequest $request)
    {
        $service = new TaskService();
        $data = $request->validated();

        $task = Task::query()->find($data['id']);
        $comment_data = $service->prepareCommentData($task, $data);

        $task->name = $data['name'];
        $task->task_stage_id = $data['task_stage_id'];
        $task->description = $data['description'] ?? '';
        $task->start = empty($data['start']) ? null : Carbon::make($data['start']);
        $task->end = empty($data['end']) ? null : Carbon::make($data['end']);
        $task->responsible_id = $data['responsible_id'] ?? backpack_user()->id;
        $task->manager_id = $data['manager_id'] ?? null;
        $task->executor_id = $data['executor_id'] ?? null;
        $task->save();

        $service->createNewMessage($task, $comment_data);
        $task->fields()->sync($data['fields'] ?? []);
        $service->updateComments($task, $data);

        $comment_count = $data['comment_count'] ?? 10;

        $task->load([
            'stage',
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
}
