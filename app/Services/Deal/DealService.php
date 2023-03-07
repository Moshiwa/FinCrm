<?php

namespace App\Services\Deal;

use App\Enums\CommentTypeEnum;
use App\Models\Comment;
use App\Models\File;
use App\Services\Button\ActionService;
use App\Services\Space\SpaceService;
use Illuminate\Support\Facades\Storage;

class DealService
{
    public function prepareCommentData($deal, $data): array
    {
        $result = [
            'comment' => [],
            'files' => []
        ];

        $action = $data['action'] ?? [];
        $result['comment'] += (new ActionService())->getActionMessage($deal, $action);
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

    public function updateClient($deal, array $data): void
    {
        $deal->client()->update([
            'name' => $data['client']['name']
        ]);

        $deal->client->fields()->sync($data['client']['fields'] ?? []);
    }

    public function updateComments($deal, array $data): void
    {
        $deal_id = $deal->id;
        $comments = $data['comments'] ?? [];

        //Здесь возможно редактирование сообщений

        $this->deleteComments($deal, $data);
    }

    public function createNewMessage($deal, $comment): void
    {
        if ($comment['comment']['type']) {
            $commentModel = $deal->comments()->create($comment['comment']);
            $files = $this->saveFiles($comment['files'] ?? [], $deal);
            $commentModel->files()->attach($files);
        }
    }

    private function deleteComments($deal, array $data)
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
