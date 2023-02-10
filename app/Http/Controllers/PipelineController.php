<?php

namespace App\Http\Controllers;

use App\Http\Requests\PipelineRequest;
use App\Models\Pipeline;

class PipelineController extends Controller
{
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

        return $pipeline;
    }

    public function delete(Pipeline $pipeline)
    {
        $pipeline->delete();
        return Pipeline::query()->with('stages')->select('id', 'name')->get();
    }


}
