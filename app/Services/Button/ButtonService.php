<?php

namespace App\Services\Button;

use App\Models\TaskStage;

class ButtonService
{
    public function mergeDealButtonsSettings($pipeline): array
    {
        $pipeline->load([
            'stages',
            'buttons' => [
                'visible',
                'action' => [
                    'responsible',
                    'pipeline',
                    'stage'
                ]
            ],
        ]);

        $pipeline = $pipeline->toArray();
        if (! empty($pipeline['buttons'])) {
            foreach ($pipeline['buttons'] as $buttonIndex => $button) {
                $pipeline['buttons'][$buttonIndex] = $this->mergeVisibleStages($pipeline['buttons'][$buttonIndex], $pipeline['stages']);
            }
        }

        return $pipeline;
    }

    public function mergeTaskButtonsSettings($task_stage): array
    {
        $task_stage->load([
            'buttons' => [
                'visible',
                'action' => [
                    'responsible',
                    'manager',
                    'executor',
                    'stage'
                ]
            ],
        ]);

        $task_stages_all = TaskStage::query()->get()->toArray();

        $task_stage = $task_stage->toArray();
        if (! empty($task_stage['buttons'])) {
            foreach ($task_stage['buttons'] as $buttonIndex => $button) {
                $task_stage['buttons'][$buttonIndex] = $this->mergeVisibleStages($button, $task_stages_all);
            }
        }

        return $task_stage;
    }

    private function mergeVisibleStages($button, $stages)
    {
        $visible_stages= $button['visible'] ?? [];

        foreach ($stages as $index => $stage) {
            $stage['is_active'] = false;
            $button['visible'][$index] = $stage;
            foreach ($visible_stages as $visible_stage) {
                if ($stage['id'] == $visible_stage['id']) {
                    $button['visible'][$index]['is_active'] = true;
                }
            }
        }

        return $button;
    }
}
