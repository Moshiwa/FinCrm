<?php

namespace App\Services\Deal;

use App\Models\Comment;

class DealPrepareService
{
    public function prepareDealData(array $data)
    {

    }

    public function prepareComments(array $data): array
    {
        $comments = [];

        if (! empty($data['comments'])) {
            foreach ($data['comments'] as $comment) {
                if (! empty($comment['id']) && ! empty($comment['content'])) {
                    $model_comment = Comment::query()->find($comment['id']);
                    if ($model_comment->exists()) {
                        $comment = $model_comment->update($comment);
                        if ($comment) {
                            $comments[] = $model_comment->id;
                        }
                    }

                    continue;
                }

                if ($comment['content']) {
                    $comment = Comment::query()->create([
                        'type' => $comment['type'] ?? '',
                        'content' => $comment['content'] ?? '',
                        'author_id' => backpack_user()->id,
                    ]);

                    $comments[] = $comment->id;
                }
            }
        }

        return $comments;
    }
}
