<?php

namespace App\Services\Button;

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

    public function definitionAction(array $action, object $deal): array
    {
        $action = $this->prepareAction($action);
        $this->definitionNewAction($action);
        $this->definitionOldAction($deal);

        return array_filter($this->actions, function ($item) {
            return !empty($item['new']) || !empty($item['old']);
        });
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

    private function definitionOldAction(object $deal): void
    {
        $deal->load(['pipeline', 'stage', 'responsible']);

        if (! empty($this->actions['change_pipeline']['new'])) {
            $this->actions['change_pipeline']['old'] = $deal->pipeline->name;
        }

        if (! empty($this->actions['change_stage']['new'])) {
            $this->actions['change_stage']['old'] = $deal->stage->name;
        }

        if (! empty($this->actions['change_responsible']['new'])) {
            $this->actions['change_responsible']['old'] = $deal->responsible->name;
        }
    }
}
