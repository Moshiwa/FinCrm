<?php

namespace App\Http\Controllers\Api;

use App\Enums\FieldsEntitiesEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\PipelineRequest;
use App\Http\Resources\FieldResource;
use App\Http\Resources\PipelineResource;
use App\Models\Field;
use App\Models\Pipeline;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FieldController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $entity = $request->get('entity');

        switch ($entity) {
            case FieldsEntitiesEnum::deal->value:
                $fields = Field::includedDeal()->get();
                break;
            case FieldsEntitiesEnum::client->value:
                $fields = Field::includedClient()->get();
                break;
            case FieldsEntitiesEnum::task->value:
                $fields = Field::includedTask()->get();
                break;
            default:
                $fields = Field::query()->get();
        }

        return FieldResource::collection($fields);
    }

    public function show(Request $request, Field $field): FieldResource
    {
        return FieldResource::make($field);
    }
}
