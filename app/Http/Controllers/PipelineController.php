<?php

namespace App\Http\Controllers;

use App\Http\Requests\PipelineRequest;
use App\Models\Pipeline;
use App\Models\Stage;

class PipelineController extends Controller
{
    public function update(PipelineRequest $request)
    {
        $data = $request->validated();

        if (empty($data['id'])) {
            return response()->json([
                'success' => false,
                'message' => 'Не указан идентификатор воронки'
            ]);
        }

        $pipeline = Pipeline::query()->find($data['id']);
        $pipeline->name = $data['name'] ?? $pipeline->name;
        $pipeline->update();

        if (empty($data['stages'])) {
            return response()->json([
                'success' => true,
                'message' => 'ok'
            ]);
        }

        foreach ($data['stages'] as $stage) {
            if (empty($stage['id'])) {
                Stage::query()->create([
                    'name' => $stage['name'] ?? '',
                    'color' => $stage['color'] ?? '',
                    'pipeline_id' => $pipeline->id,
                ]);
            } else {
                $stage = Stage::query()->find($stage['id']);
                $stage->name = $stage['name'] ?? $stage->name;
                $stage->color = $stage['color'] ?? $stage->color;
                $stage->pipeline_id = $pipeline->id;
                $stage->update();
            }
        }
    }

    public function get(Pipeline $pipeline)
    {
        $pipeline->load([
            'stages',
            'stages.settings'
        ]);

        return $pipeline;
    }

    public function create(PipelineRequest $request)
    {
        $data = $request->validated();
        $pipeline = Pipeline::query()->create([
            'name' => $data['name'],
        ]);

        $pipeline->load('stages');

        return response()->json([
            'success' => true,
            'message' => 'ok',
            'data' => $pipeline
        ]);
    }

    public function delete(Pipeline $pipeline)
    {
        $deal_exist = $pipeline->stages()->whereHas('deals')->exists();
        if ($deal_exist) {
            return response()->json([
                'success' => false,
                'message' => 'У воронки имеется сделка',
            ]);
        }

        $pipeline->delete();
        return response()->json([
            'success' => true,
            'message' => 'ok'
        ]);
    }


}
