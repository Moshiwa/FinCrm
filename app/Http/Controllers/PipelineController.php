<?php

namespace App\Http\Controllers;

use App\Http\Requests\PipelineRequest;
use App\Models\Pipeline;
use App\Models\Stage;
use Illuminate\Support\Arr;

class PipelineController extends Controller
{
    private array $errors = [];

    public function update(PipelineRequest $request)
    {
        $data = $request->validated();

        if (empty($data['id'])) {
            $this->errors[] = 'Не указан идентификатор воронки';
            return response()->json([
                'errors' => $this->errors
            ]);
        }

        $pipeline = Pipeline::query()->find($data['id']);
        $pipeline->name = $data['name'] ?? $pipeline->name;
        $pipeline->save();

        if (! empty($data['deleted_stages'])) {
            $stages = Stage::query()->with('deals', function ($query) {
                $query->select('id');
            })->whereIn('id', $data['deleted_stages'])->get();
            foreach ($stages as $stage) {
                if ($stage->deals()->exists()) {
                    $this->errors[] = 'У стадии ' . $stage->name . ' имеется сделка';
                    continue;
                }

                $stage->delete();
            }
        }

        if (! empty($data['stages'])) {
            foreach ($data['stages'] as $stage) {
                $stage['color'] = $stage['color'] ?? '#FFFFFF';
                $stage['pipeline_id'] = $pipeline->id;
                if (empty($stage['id'])) {
                    Stage::query()->create([
                        'name' => $stage['name'],
                        'color' => $stage['color'],
                        'pipeline_id' => $stage['pipeline_id']
                    ]);
                } else {
                    Stage::query()->firstOrCreate(['id' => $stage['id']], $stage);
                }
            }
        }

        $pipeline->load(['stages']);

        return response()->json([
            'errors' => $this->errors,
            'data' => $pipeline
        ]);
    }

    public function create(PipelineRequest $request)
    {
        $data = $request->validated();
        $pipeline = Pipeline::query()->create([
            'name' => $data['name'],
        ]);

        $pipeline->load('stages');

        return response()->json([
            'errors' => $this->errors,
            'data' => $pipeline
        ]);
    }

    public function delete(Pipeline $pipeline)
    {
        $deal_exist = $pipeline->stages()->whereHas('deals')->exists();
        if ($deal_exist) {
            $this->errors[] = 'У воронки имеется сделка';
            return response()->json([
                'errors' => $this->errors,
            ]);
        }

        $pipeline->delete();
        return response()->json([
            'errors' => $this->errors,
        ]);
    }


}
