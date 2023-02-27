<?php

namespace App\Services\Deal;

use App\Enums\CommentTypeEnum;
use App\Models\DealComment;
use App\Models\File;
use App\Models\Pipeline;
use App\Models\Stage;
use App\Models\User;
use App\Services\Space\SpaceService;
use Illuminate\Support\Facades\Storage;

class DealService
{

    public function updateClient($deal, array $data): void
    {
        $deal->client()->update([
            'name' => html_entity_decode($data['client']['name'] ?? '')
        ]);

        $deal->client->fields()->sync($data['client']['fields'] ?? []);
    }

    public function updateComments($deal, array $data): void
    {
        $deal_id = $deal->id;
        $comments = $data['comments'] ?? [];

        foreach ($comments as $comment) {
            $model_comment = DealComment::query()->find($comment['id']);
            $model_comment->update([
                'type' => $comment['type'] ?? CommentTypeEnum::comment,
                'content' => html_entity_decode($comment['content']),
                'deal_id' => $comment['deal_id'] ?? $deal_id,
                'author_id' => backpack_user()->id,
            ]);
        }

        $this->deleteComments($deal, $data);
    }

    private function deleteComments($deal, array $data)
    {
        if (! empty($data['delete_comment_id'])) {
            $model_comment = DealComment::query()->find($data['delete_comment_id']);
            foreach ($model_comment->files as $file) {
                $file->delete();
            }

            $model_comment->files()->sync([]);
            $model_comment->delete();
        }
    }

    public function createNewMessage($deal, $data): void
    {
        $title = $this->definitionTitleComment($deal);
        $comment = $data['new_comment'] ?? [];
        if (empty($title)) {
            if (!empty($comment)) {
                $title = 'Комментарий';
                $files = $this->saveFiles($comment['files'] ?? [], $deal);
                $comment_model = $deal->comments()->create([
                    'title' => $title,
                    'content' => $comment['content'],
                    'type' => empty($files) ? CommentTypeEnum::comment->value : CommentTypeEnum::document->value,
                    'author_id' => backpack_user()->id,
                ]);

                $comment_model->files()->attach($files);
            }
        } else {
            $deal->comments()->create([
                'content' => $data['new_comment']['content'] ?? '',
                'title' => $title,
                'type' => CommentTypeEnum::action->value,
                'author_id' => backpack_user()->id,
            ]);
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

    private function definitionTitleComment($deal): string
    {
        $actions = [];

        $new_deal = $deal->getAttributes();

        if ($deal->isDirty('pipeline_id')) {
            $new_deal['pipeline'] = Pipeline::query()->find($new_deal['pipeline_id'])->toArray();
            $actions[] = 'Смена воронки с <i>' . $deal->pipeline->name . '</i> на <i>' . $new_deal['pipeline']['name'] . '</i>';
        }

        if ($deal->isDirty('stage_id')) {
            $new_deal['stage'] = Stage::query()->find($new_deal['stage_id'])->toArray();
            $actions[] = 'Смена стадии с <i>' . $deal->stage->name . '</i> на <i>' . $new_deal['stage']['name'] . '</i>';
        }

        if ($deal->isDirty('responsible_id')) {
            $new_deal['responsible'] = User::query()->find($new_deal['responsible_id'])->toArray();
            $actions[] = 'Смена ответственного с <i>' . $deal->responsible->name . '</i> на <i>' . $new_deal['responsible']['name'] . '</i>';
        }


        return implode('<br>', $actions);
    }
}
