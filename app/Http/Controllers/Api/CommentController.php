<?php

namespace App\Http\Controllers\Api;

use App\Enums\CommentTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CommentRequest;
use App\Models\Comment;
use App\Models\Deal;
use App\Models\Task;

class CommentController extends Controller
{
    public function store(CommentRequest $request)
    {
        $data = $request->validated();

        $entity = null;
        switch ($data['entity']) {
            case 'deal':
                $entity = Deal::query()->find($data['entity_id']);
                break;
            case 'task':
                $entity = Task::query()->find($data['entity_id']);
                break;
        }

        if (empty($entity)) {
            response()->json([
                'success' => false,
                'message' => 'Сущность не найдена'
            ], 400);
        }

        $comment = $entity->comments()->create([
            'type' => CommentTypeEnum::COMMENT->value,
            'title' => 'Комментарий',
            'content' => $data['content'],
            'author_id' => backpack_user()->id
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $comment->id
            ]
        ]);
    }

    public function destroy(Comment $comment)
    {
        if (!(backpack_user()->id == $comment->author_id || backpack_user()->hasRole('admin'))) {
            return response()->json([
                'success' => false,
                'message' => 'У вас недостаточно прав'
            ], 403);
        }

        $comment->delete();

        return response()->json([
            'success' => true,
        ]);
    }
}
