<?php

namespace App\Services\Button;

use App\Enums\ActionsEnum;
use App\Enums\CommentTypeEnum;
use App\Models\Comment;
use Illuminate\Support\Str;

class ActionService
{
    protected array $actions = [];

    public function __construct()
    {
        foreach (ActionsEnum::cases() as $case) {
            $this->actions[$case->value] = [
                'old' => null,
                'new' => null,
            ];
        }
    }

    public function definitionAction(array $action, object $entity): array
    {
        $action = $this->prepareAction($action);

        $this->definitionNewAction($action);
        $this->definitionOldAction($entity);

        return array_filter($this->actions, function ($item) {
            return !empty($item['new']) || !empty($item['old']);
        });
    }

    public function getActionMessage(object $entity, array $action): array
    {
        $actionService = new ActionService();
        $actions = $actionService->definitionAction($action, $entity);

        $comment = [
            'title' => '',
            'type' => '',
            'content' => ''
        ];

        $action_comment = '';

        foreach ($actions as $action_name => $value) {
            $action_name = ActionsEnum::fromValue($action_name)?->value;
            if ($action_name === ActionsEnum::COMMENT->value) {
                $comment['title'] = ActionsEnum::getMessageTemplate($action_name);
                $comment['type'] = CommentTypeEnum::COMMENT->value;
                continue;
            }

            $text = '';
            $value['old'] = $value['old'] ?? '';
            $value['new'] = $value['new'] ?? '';
            if ($value['old'] != $value['new']) {
                $text = ActionsEnum::getMessageTemplate($action_name);
                $text .= empty($value['old']) ? '' :  ' с <i style="color: #0B90C4">' . $value['old'] . '</i>';
                $text .= empty($value['new']) ? '' :  ' на <i style="color: #0B90C4">' . $value['new'] . '</i><br>';
            }

            $action_comment .= $text;
        }

        if (!empty($action_comment)) {
            $comment['title'] = $action_comment;
            $comment['type'] = CommentTypeEnum::ACTION->value;
        }

        return $comment;
    }

    private function prepareAction(array $actions): array
    {
        $result = [];

        foreach ($actions as $action_name => $action) {
            $sys_action = ActionsEnum::fromValue($action_name)?->value;
            $entity = ActionsEnum::getEntity($sys_action);
            $value = $action;
            if ($entity) {
                if (class_exists($entity)) {
                    $value = $entity::query()->select('id', 'name')->find($action)->toArray();
                }
            }

            $result[$sys_action] = $value;
        }

        return $result;
    }

    private function definitionNewAction(array $actions): void
    {
        foreach ($actions as $action_name => $value) {
            if (empty($value)) {
                continue;
            }

            $this->actions[ActionsEnum::fromValue($action_name)?->value] = [
                'new' => $value['name'] ?? $value
            ];
        }
    }

    private function definitionOldAction(object $entity): void
    {
        foreach ($this->actions as $action_name => $action) {
            if (empty($action['new'])) {
                continue;
            }

            $relation = ActionsEnum::getRelation($action_name);
            $relations = explode('.', $relation);

            if (empty($relations[0])) {
                continue;
            }

            if ($entity->{$relations[0]}) {
                $this->actions[$action_name]['old'] = $entity->{$relations[0]};
                if (isset($relations[1])) {
                    $this->actions[$action_name]['old'] = $entity->{$relations[0]}->{$relations[1]};
                }
            }
        }
    }
}
