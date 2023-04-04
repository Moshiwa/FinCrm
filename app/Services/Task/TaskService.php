<?php

namespace App\Services\Task;

use App\Enums\CommentTypeEnum;
use App\Models\Comment;
use App\Models\File;
use App\Services\Button\ActionService;
use App\Services\Space\SpaceService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class TaskService
{
    public function updateTask($task, $data)
    {
        if (backpack_user()->can('tasks.update')) {
            $task->name = $data['name'];
            if (backpack_user()->can('tasks.change_stage')) {
                $task->task_stage_id = $data['task_stage_id'];
            }

            $task->description = $data['description'] ?? '';
            $task->start = empty($data['start']) ? null : Carbon::make($data['start']);
            $task->end = empty($data['end']) ? null : Carbon::make($data['end']);

            if (backpack_user()->can('tasks.change_members_self')) {
                if (backpack_user()->id == $task->responsible_id || backpack_user()->hasRole('admin')) {
                    $task->responsible_id = $data['responsible_id'] ?? backpack_user()->id;
                    $task->manager_id = $data['manager_id'] ?? null;
                    $task->executor_id = $data['executor_id'] ?? null;
                }
            }

            if (backpack_user()->can('tasks.change_responsible')) {
                $task->responsible_id = $data['responsible_id'] ?? backpack_user()->id;
            }

            $task->save();
        }

        return $task;
    }

    public function prepareCommentData($task, $data): array
    {
        $result = [
            'comment' => [],
            'files' => []
        ];

        $result['comment'] += (new ActionService())->getActionMessage($task, $data);
        $result['comment']['content'] = $data['new_comment']['content'] ?? '';
        $result['comment']['author_id'] = backpack_user()->id;

        $result['files'] += $data['new_comment']['files'] ?? [];

        if (empty($result['comment']['type'])) {
            if (! empty($result['files'])) {
                $result['comment']['title'] = 'Документы';
                $result['comment']['type'] = CommentTypeEnum::DOCUMENT->value;
            }
        }

        return $result;
    }

    public function updateComments($task, array $data): void
    {
        $task_id = $task->id;
        $comments = $data['comments'] ?? [];

        //Здесь возможно редактирование сообщений

        $this->deleteComments($data);
    }

    public function createNewMessage($task, $comment): void
    {
        if ($comment['comment']['type']) {
            $commentModel = $task->comments()->create($comment['comment']);
            $files = $this->saveFiles($comment['files'] ?? [], $task);
            $commentModel->files()->attach($files);
        }
    }

    private function deleteComments(array $data)
    {
        if (! empty($data['delete_comment_id'])) {
            $model_comment = Comment::query()->find($data['delete_comment_id']);
            //Action нельзя удалять
            if ($model_comment->type === CommentTypeEnum::ACTION->value) {
                $model_comment->update(['content' => '']);
            } else {
                foreach ($model_comment->files as $file) {
                    $file->delete();
                }

                $model_comment->files()->sync([]);
                $model_comment->delete();
            }
        }
    }

    private function saveFiles($files, $deal)
    {
        $files = is_array($files) ? $files : [$files];
        $result = [];
        foreach ($files as $file) {
            if (! is_uploaded_file($file)) {
                continue;
            }

            $space = SpaceService::getCurrentSpaceCode();
            $name = "/task_$space/$deal->id";
            $path = Storage::disk('public')->put($name, $file);

            $file = File::query()->create([
                'size' => $file->getSize(),
                'meme' => $file->getClientOriginalExtension(),
                'original_name' => $file->getClientOriginalName(),
                'path' => $path
            ]);

            $result[] = $file->id;
        }

        return $result;
    }
}
