<?php

namespace App\Services\Button;

use App\Models\DealComment;
use App\Models\Pipeline;
use App\Models\Stage;
use App\Models\User;

class ActionService
{
    const COMMENT = 'comment';
    const CHANGE_PIPELINE = 'change_pipeline';
    const CHANGE_STAGE = 'change_stage';
    const CHANGE_RESPONSIBLE = 'change_responsible';

    protected array $actions = [
        self::COMMENT => [
            'new' => false,
        ],
        self::CHANGE_PIPELINE => [
            'old' => null,
            'new' => null,
        ],
        self::CHANGE_STAGE => [
            'old' => null,
            'new' => null,
        ],
        self::CHANGE_RESPONSIBLE => [
            'old' => null,
            'new' => null,
        ]
    ];

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
            if ($action_name === $actionService::COMMENT) {
                $comment['title'] = 'Комментарий';
                $comment['type'] = DealComment::COMMENT;
            }

            if ($action_name === $actionService::CHANGE_PIPELINE) {
                if ($value['old'] !== $value['new']) {
                    $action_comment .= 'Смена воронки с <i style="color: #0B90C4">' . $value['old'] . '</i> на <i style="color: #0B90C4">' . $value['new'] . '</i><br>';
                }
            }

            if ($action_name === $actionService::CHANGE_STAGE) {
                if ($value['old'] !== $value['new']) {
                    $action_comment .= 'Смена стадии с <i style="color: #0B90C4">' . $value['old'] . '</i> на <i style="color: #0B90C4">' . $value['new'] . '</i><br>';
                }
            }

            if ($action_name === $actionService::CHANGE_RESPONSIBLE) {
                if ($value['old'] !== $value['new']) {
                    $action_comment .= 'Смена ответственного с <i style="color: #0B90C4">' . $value['old'] . '</i> на <i style="color: #0B90C4">' . $value['new'] . '</i><br>';
                }
            }
        }

        if (!empty($action_comment)) {
            $comment['title'] = $action_comment;
            //ToDo вынести константы в enum
            $comment['type'] = DealComment::ACTION;
        }

        return $comment;
    }

    private function prepareAction(array $action): array
    {
        $result = [];
        $result['pipeline'] = !empty($action['pipeline_id'])
            ? Pipeline::query()->select('id', 'name')->find($action['pipeline_id'])->toArray()
            : null;
        $result['stage'] = !empty($action['stage_id'])
            ? Stage::query()->select('id', 'name')->find($action['stage_id'])->toArray()
            : null;
        $result['responsible'] = !empty($action['responsible_id'])
            ? User::query()->select('id', 'name')->find($action['responsible_id'])->toArray()
            : null;
        $result['comment'] = !empty($action['comment']);

        return $result;
    }

    private function definitionNewAction(array $action): void
    {
        if (! empty($action['comment'])) {
            $this->actions[self::COMMENT] = [
                'new' => true
            ];
        }

        if (! empty($action['pipeline'])) {
            $this->actions[self::CHANGE_PIPELINE] = [
                'new' => $action['pipeline']['name']
            ];
        }

        if (! empty($action['stage'])) {
            $this->actions[self::CHANGE_STAGE] = [
                'new' => $action['stage']['name']
            ];
        }

        if (! empty($action['responsible'])) {
            $this->actions[self::CHANGE_RESPONSIBLE] = [
                'new' => $action['responsible']['name']
            ];
        }
    }

    private function definitionOldAction(object $entity): void
    {
        $entity = $this->entityLoadRelations($entity);

        if (! empty($this->actions['change_pipeline']['new'])) {
            $this->actions['change_pipeline']['old'] = $entity->pipeline->name;
        }

        if (! empty($this->actions['change_stage']['new'])) {
            $this->actions['change_stage']['old'] = $entity->stage->name;
        }

        if (! empty($this->actions['change_responsible']['new'])) {
            $this->actions['change_responsible']['old'] = $entity->responsible->name;
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
