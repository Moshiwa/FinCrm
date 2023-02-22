<?php

namespace App\Services\Button;

use App\Models\Action;
use App\Models\Stage;
use App\Models\User;

class ButtonService
{
    public function mergeButtonsSettings($pipeline): array
    {
        $pipeline->load(['stages', 'buttons' => ['visible', 'action']]);
        $pipeline = $pipeline->toArray();
        if (! empty($pipeline['buttons'])) {
            foreach ($pipeline['buttons'] as $buttonIndex => $button) {
                $pipeline['buttons'][$buttonIndex] = $this->mergeVisibleStages($pipeline['buttons'][$buttonIndex], $pipeline['stages']);
                //$result[$index]['buttons'][$buttonIndex] = $this->mergeActions($result[$index]['buttons'][$buttonIndex], $actions);
            }
        }

        return $pipeline;
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
