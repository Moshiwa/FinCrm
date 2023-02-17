<?php

namespace App\Services\Deal;

use App\Enums\CommentTypeEnum;
use App\Enums\FilesTypeEnum;
use App\Models\DealComment;
use App\Models\File;
use App\Services\Space\SpaceService;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Storage;

class DealService
{
    public function definitionCommentType($data): string
    {
        if (is_uploaded_file($data)) {
            $ext = $data->getClientOriginalExtension();
            switch ($ext) {
                case 'png':
                case 'jpg':
                case 'jpeg':
                case 'doc':
                case 'pdf':
                case 'txt':
                    return CommentTypeEnum::document->value;
            }
        }

        return CommentTypeEnum::comment->value;
    }

    public function saveDeal($deal, array $data): void
    {
        $deal->update([
            'name' => html_entity_decode($data['name'] ?? ''),
            'pipeline_id' => $data['pipeline_id'],
            'client_id' => $data['client_id'],
            'stage_id' => $data['stage_id'],
            'responsible_id' => $data['responsible_id'],
        ]);

        $deal->fields()->sync($data['fields'] ?? []);
    }

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
            if (empty($comment['id'])) {
                $model_comment = DealComment::query()->create([
                    'type' => $comment['type'] ?? CommentTypeEnum::comment,
                    'content' => html_entity_decode($comment['content']),
                    'deal_id' => $comment['deal_id'] ?? $deal_id,
                    'author_id' => backpack_user()->id,
                ]);

                if (empty($comment['files'])) {
                    continue;
                }

                $files = is_array($comment['files'])
                    ? $comment['files']
                    : [$comment['files']];

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

                    $model_comment->files()->attach($file->id);

                }

            } else {
                $model_comment = DealComment::query()->find($comment['id']);
                $model_comment->update([
                    'type' => $comment['type'] ?? CommentTypeEnum::comment,
                    'content' => html_entity_decode($comment['content']),
                    'deal_id' => $comment['deal_id'] ?? $deal_id,
                    'author_id' => backpack_user()->id,
                ]);
            }
        }

        if (! empty($data['delete_comment_id'])) {
            $model_comment = DealComment::query()->find($data['delete_comment_id']);
            foreach ($model_comment->files as $file) {
                $file->delete();
            }

            $model_comment->files()->sync([]);
            $model_comment->delete();
        }
    }
}
