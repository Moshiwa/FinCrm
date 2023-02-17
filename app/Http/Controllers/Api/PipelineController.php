<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PipelineRequest;
use App\Http\Resources\PipelineResource;
use App\Models\Pipeline;
use Illuminate\Http\Request;

class PipelineController extends Controller
{
    public function index(Request $request)
    {
        $with = $request->get('with', 'stages');

        $pipelines = Pipeline::query()
            ->with($with)
            ->get();

        return PipelineResource::collection($pipelines);
    }

    public function show(Request $request, Pipeline $pipeline)
    {
        $with = $request->get('with', 'stages');
        $pipeline->load($with);

        return PipelineResource::make($pipeline);
    }

    public function store(PipelineRequest $request, Pipeline $pipeline)
    {

    }
}
