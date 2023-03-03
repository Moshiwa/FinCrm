<?php

namespace App\Services\Task;

use App\Models\DealComment;
use App\Models\File;
use App\Services\Button\ActionService;
use App\Services\Space\SpaceService;
use Illuminate\Support\Facades\Storage;

class TaskService
{
    public function prepareCommentData($task, $data): array
    {
        $result = [
            'comment' => [],
            'files' => []
        ];

        $action = $data['action'] ?? [];
        $result['comment'] += (new ActionService())->getActionMessage($task, $action);
        $result['comment']['content'] = $data['new_comment']['content'] ?? '';
        $result['comment']['author_id'] = backpack_user()->id;

        $result['files'] += $data['new_comment']['files'] ?? [];

        if (empty($result['comment']['type'])) {
            if (! empty($result['files'])) {
                $result['comment']['title'] = 'Документы';
                $result['comment']['type'] = DealComment::DOCUMENT;
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
            $model_comment = DealComment::query()->find($data['delete_comment_id']);
            //Action нельзя удалять
            if ($model_comment->type === DealComment::ACTION) {
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
            $name = "/deal_$space/$deal->id";
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
