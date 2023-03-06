<?php

namespace App\Services\Button;

use App\Enums\ActionsEnum;
use App\Models\DealComment;
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
            $action_name = ActionsEnum::fromName($action_name)?->value;

            if ($action_name === ActionsEnum::COMMENT->value) {
                $comment['title'] = ActionsEnum::getMessageTemplate($action_name);
                $comment['type'] = DealComment::COMMENT;
                continue;
            }

            $text = '';
            $value['old'] = $value['old'] ?? '';
            $value['new'] = $value['new'] ?? '';
            if ($value['old'] != $value['new']) {
                $text = ActionsEnum::getMessageTemplate($action_name);
                $text = Str::replaceArray('[ActionValue]', [$value['old'], $value['new']], $text);
            }

            $action_comment .= $text;
        }

        if (!empty($action_comment)) {
            $comment['title'] = $action_comment;
            //ToDo вынести константы в enum
            $comment['type'] = DealComment::ACTION;
        }

        return $comment;
    }

    private function prepareAction(array $actions): array
    {
        $result = [];

        foreach ($actions as $action_name => $action) {
            $sys_action = ActionsEnum::fromName($action_name)?->value;
            $entity = ActionsEnum::getEntity($sys_action);
            $value = $action;
            if ($entity) {
                $entity = "App\\Models\\" . $entity;
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

            $this->actions[ActionsEnum::fromName($action_name)?->value] = [
                'new' => $value['name'] ?? $value
            ];
        }
    }

    private function definitionOldAction(object $entity): void
    {
        /*$entity = $this->entityLoadRelations($entity);*/
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

    private function entityLoadRelations($entity)
    {
        $load = [];
        $load[] = $entity->pipeline?->exists() ? 'pipeline' : '';
        $load[] = $entity->stage?->exists() ? 'stage' : '';
        $load[] = $entity->responsible?->exists() ? 'responsible' : '';
        $load[] = $entity->executor?->exists() ? 'executor' : '';
        $load[] = $entity->manager?->exists() ? 'manager' : '';
        $load = array_filter($load);
        $entity->load($load);

        return $entity;
    }
}
