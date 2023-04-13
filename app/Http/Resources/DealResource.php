<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DealResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'fields' => $this->fields,
            'pipeline' => $this->pipeline,
            'stage' => $this->stage,
            'client' => ClientResource::make($this->client ?? null),
            'responsible' => UserResource::make($this->responsible ?? []),
            'comments' => $this->comments,
        ];
    }
}
