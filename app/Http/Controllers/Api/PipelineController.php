<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PipelineRequest;
use App\Http\Resources\PipelineResource;
use App\Models\Pipeline;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PipelineController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $pipelines = Pipeline::query()->get();

        return PipelineResource::collection($pipelines);
    }

    public function show(Request $request, Pipeline $pipeline): PipelineResource
    {
        return PipelineResource::make($pipeline);
    }
}
