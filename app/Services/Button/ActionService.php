<?php

namespace App\Services\Button;

use App\Enums\ActionsEnum;
use App\Enums\CommentTypeEnum;
use App\Models\Comment;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ActionService
{
    protected array $actions = [];

    protected string $new_data_color = '#0B90C4';
    protected string $old_data_color = '#0B90C4';

    public function __construct()
    {
        foreach (ActionsEnum::cases() as $case) {
            $this->actions[$case->value] = [
                'old' => null,
                'new' => null,
            ];
        }
    }

    public function getActionMessage(object $entity, array $save_data): array
    {
        $actionService = new ActionService();
        $actions = $actionService->definitionAction($entity, $save_data);

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
                $text .= empty($value['old']) ? '' :  ' с <i style="color: ' . $this->new_data_color . '">' . $value['old'] . '</i>';
                $text .= empty($value['new']) ? '' :  ' на <i style="color: ' . $this->old_data_color . '">' . $value['new'] . '</i><br>';
            }

            $action_comment .= $text;
        }

        if (!empty($action_comment)) {
            $comment['title'] = $action_comment;
            $comment['type'] = CommentTypeEnum::ACTION->value;
        }

        return $comment;
    }

    private function definitionAction(object $entity, array $save_data): array
    {
        $ready_action = [];
        $actions = ActionsEnum::definitionActionsByRequest();
        foreach ($actions as $action_name => $request_path) {
            $paths = explode('.', $request_path);
            $node = $save_data;
            foreach ($paths as $path) {
                if (isset($node[$path])) {
                    $node = $node[$path];
                } else {
                    break;
                }
            }

            $node = is_array($node) ? null : $node;

            $ready_action[$action_name] = $node;
            if ($action_name === ActionsEnum::COMMENT->value) {
                $ready_action[$action_name] = !empty($node);
            }
        }

        $action = $this->prepareAction($ready_action);

        $this->definitionNewAction($action);
        $this->definitionOldAction($entity);

        return array_filter($this->actions, function ($item) {
            return !empty($item['new']) || !empty($item['old']);
        });
    }

    private function prepareAction(array $actions): array
    {
        $result = [];

        foreach ($actions as $action_name => $action) {
            $sys_action = ActionsEnum::fromValue($action_name)?->value;
            $entity = ActionsEnum::getEntity($sys_action);
            $value = $action;
            if ($entity && is_numeric($action)) {
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

            $value = $value['name'] ?? $value;
            $value = strtotime($value) ? Carbon::make($value)->translatedFormat('j F Y H:i') : $value;

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
                $value = $entity->{$relations[0]};
                $this->actions[$action_name]['old'] = $value;
                if (isset($relations[1])) {
                    $value = $entity->{$relations[0]}->{$relations[1]};
                    $this->actions[$action_name]['old'] = $value;
                }

                $this->actions[$action_name]['old'] = strtotime($this->actions[$action_name]['old'])
                    ? Carbon::make($this->actions[$action_name]['old'])->translatedFormat('j F Y H:i')
                    : $this->actions[$action_name]['old'];

            }
        }
    }
}
