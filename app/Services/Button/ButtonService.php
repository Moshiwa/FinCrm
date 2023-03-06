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

    public function mergeTaskButtonsSettings($buttons): array
    {
        $buttons = $buttons->toArray();

        $all_stages = TaskStage::query()->get()->toArray();

        $result = [];

        foreach ($buttons as $buttonIndex => $button) {
            $result[] = $this->mergeVisibleStages($button, $all_stages);
        }

        return $result;
    }

    private function mergeVisibleStages($button, $stages)
    {
        $visible_stages = $button['visible'] ?? [];

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
