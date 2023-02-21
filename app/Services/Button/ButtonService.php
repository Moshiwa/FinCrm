<?php

namespace App\Services\Button;

use App\Models\Action;
use App\Models\Stage;
use App\Models\User;

class ButtonService
{
    public function mergeButtonsSettings($pipelines): array
    {
        $result = [];

        foreach ($pipelines as $index => $pipeline) {
            $pipeline = $pipeline->toArray();
            $result[$index] = $pipeline;
            if (! empty($pipeline['buttons'])) {
                foreach ($pipeline['buttons'] as $buttonIndex => $button) {
                    $result[$index]['buttons'][$buttonIndex] = $this->mergeVisibleStages($result[$index]['buttons'][$buttonIndex], $pipeline['stages']);
                    //$result[$index]['buttons'][$buttonIndex] = $this->mergeActions($result[$index]['buttons'][$buttonIndex], $actions);
                }
            }
        }

        return $result;
    }

    private function mergeVisibleStages($button, $stages)
    {
        $visible_stage_ids = $button['options']['display']['stages'] ?? [];

        foreach ($stages as $index => $stage) {
            $button['options']['display']['stages'][$index] = [
                'id' => $stage['id'],
                'name' => $stage['name'],
                'is_active' => false,
            ];
            foreach ($visible_stage_ids as $visible_stage_id) {
                if ($stage['id'] == $visible_stage_id) {
                    $button['options']['display']['stages'][$index]['is_active'] = true;
                }
            }
        }

        return $button;
    }


    private function mergeActions($button, $actions)
    {
        $button_actions = $button['actions'] ?? [];
        foreach ($actions as $index => $action) {
            $action['is_active'] = false;
            $button['actions'][$index] = $action;
            foreach ($button_actions as $button_action) {
                if ($action['id'] === $button_action['id']) {
                    $button['actions'][$index] = $button_action;
                    $button['actions'][$index]['pivot'] = $this->defineActionEntity($button_action);
                    $button['actions'][$index]['is_active'] = true;
                }
            }
        }

        return $button;
    }

    private function defineActionEntity($action)
    {
        if ($action['name'] === 'change_stage') {
            $entity = Stage::query()->select('id', 'name');
            $entity_id = $action['pivot']['value'];
            return $entity->find($entity_id)->toArray();
        }

        if ($action['name'] === 'change_responsible') {
            $entity = User::query()->select('id', 'name');
            $entity_id = $action['pivot']['value'];
            return $entity->find($entity_id)->toArray();
        }

        return $action['pivot'];
    }
}
