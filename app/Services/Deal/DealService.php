<?php

namespace App\Services\Deal;

use App\Enums\CommentTypeEnum;
use App\Models\DealComment;
use Illuminate\Database\Query\Builder;

class DealService
{
    public function definitionCommentType($data)
    {
        if (is_uploaded_file($data)) {
            $ext = $data->getClientOriginalExtension();
            switch ($ext) {
                case 'png':
                case 'jpg':
                case 'jpeg':
                    return CommentTypeEnum::image->value;
                case 'doc':
                case 'pdf':
                case 'txt':
                    return CommentTypeEnum::document->value;
            }
        }

        return CommentTypeEnum::text;
    }

    public function saveDeal($deal, array $data): void
    {
        $deal->update([
            'name' => $data['name'] ?? '',
            'comment' => $data['comment'] ?? '',
            'pipeline_id' => $data['pipeline_id'],
            'client_id' => $data['client_id'],
            'stage_id' => $data['stage_id'],
            'responsible_id' => $data['responsible_id'],
        ]);
    }

    public function updateClient($deal, array $data): void
    {
        $deal->client()->update([
            'name' => $data['client']['name'] ?? ''
        ]);

        $client = $deal->client;
        $fields = [];

        foreach ($data['client']['fields'] as $field) {

            $fields[$field['id']] = ['value' => $field['pivot']['value']];
        }

        $client->fields()->sync($fields);
        dd($client);
    }

    public function updateComments($deal, array $data): void
    {
        $deal_id = $deal->id;

        if (! empty($data['comments'])) {
            foreach ($data['comments'] as $comment) {
                if (empty($comment['id']) && ! empty($comment['content'])) {
                    DealComment::query()->create([
                        'type' => $this->definitionCommentType($comment['type'] ?? ''),
                        'content' => $comment['content'],
                        'deal_id' => $deal_id,
                        'author_id' => backpack_user()->id,
                    ]);
                } elseif (! empty($comment['id']) && empty($comment['content'])) {
                    $model_comment = DealComment::query()->find($comment['id']);
                    $model_comment->delete();
                } elseif (! empty($comment['id']) && ! empty($comment['content'])) {
                    $model_comment = DealComment::query()->find($comment['id']);
                    $model_comment->update([
                        'content' => $comment['content'],
                        'author_id' => backpack_user()->id,
                    ]);
                }
            }
        }
    }
}
