<?php

namespace App\Services\Button;

use App\Enums\ActionsEnum;
use App\Enums\CommentTypeEnum;
use App\Models\Client;
use App\Models\Comment;
use App\Models\Deal;
use App\Models\Field;
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

            //Исключение для обычного комментария
            if ($action_name === ActionsEnum::COMMENT->value) {
                $comment['title'] = ActionsEnum::getMessageTemplate($action_name);
                $comment['type'] = CommentTypeEnum::COMMENT->value;
                continue;
            }

            //Исключение для кастомных полей
            if ($action_name === ActionsEnum::CHANGE_CUSTOM_FIELD->value) {
                $value['old'] = $value['old'] ?? '';
                $value['new'] = $value['new'] ?? '';
                if ($value['old'] != $value['new']) {
                    $value['old'] = $value['old'] === 'false' ? 'Выкл.' : $value['old'];
                    $value['old'] = $value['old'] === 'true' ? 'Вкл.' : $value['old'];
                    $value['new'] = $value['new'] === 'false' ? 'Выкл.' : $value['new'];
                    $value['new'] = $value['new'] === 'true' ? 'Вкл.' : $value['new'];

                    $field_name = Field::query()->find($value['field_id'])->name;

                    $text = ActionsEnum::getMessageTemplate($action_name);
                    if (empty($value['old'])) {
                        $text .= empty($value['new']) ? '' :  ' ' . $field_name . ' на <i style="color: ' . $this->old_data_color . '">' . $value['new'] . '</i><br>';
                    } else {
                        $text .= ' ' . $field_name . ' с <i style="color: ' . $this->new_data_color . '">' . $value['old'] . '</i>';
                        $text .= empty($value['new']) ? '' : ' на <i style="color: ' . $this->old_data_color . '">' . $value['new'] . '</i><br>';
                    }

                    $comment['title'] = $text;
                    $comment['type'] = CommentTypeEnum::ACTION->value;
                }

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
        $action['change_custom_field'] = $save_data['change_custom_field'] ?? [];

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

            if ($action_name === ActionsEnum::CHANGE_CUSTOM_FIELD->value) {
                $this->actions[ActionsEnum::CHANGE_CUSTOM_FIELD->value] = [
                    'new' => $value['value'] ?? '',
                    'client_id' => $value['client_id'] ?? '',
                    'deal_id' => $value['deal_id'] ?? '',
                    'task_id' => $value['task_id'] ?? '',
                    'field_id' => $value['field_id'] ?? '',
                ];

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

            if ($action_name === ActionsEnum::CHANGE_CUSTOM_FIELD->value) {
                $field = null;
                $field_id = $action['field_id'] ?? '';
                if (! empty($action['client_id'])) {
                    $field = $entity->client->fields->find($field_id);
                } elseif (! empty($action['deal_id'])) {
                    $field = $entity->fields->find($field_id);
                } elseif (! empty($action['task_id'])) {
                    $field = $entity->fields->find($field_id);
                }

                if (empty($field->pivot?->value)) {
                    continue;
                }

                $this->actions[ActionsEnum::CHANGE_CUSTOM_FIELD->value] += [
                    'old' => $field->pivot?->value ?? ''
                ];

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
