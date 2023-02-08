<?php

namespace App\Http\Controllers;

use App\Http\Requests\DealRequest;
use App\Models\Deal;
use App\Models\DealComment;
use App\Models\Pipeline;
use App\Services\Deal\DealService;
use App\Services\Space\SpaceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DealController extends Controller
{
    private DealService $service;

    public function __construct()
    {
        $this->service = new DealService();
    }

    public function update(DealRequest $request)
    {
        $data = $request->validated();

        $deal = Deal::query()->find($data['id']);
        $this->service->saveDeal($deal, $data);
        $this->service->updateClient($deal, $data);
        $this->service->updateComments($deal, $data);
    }

    public function getStagesByPipeline(Pipeline $pipeline)
    {
        return $pipeline->stages()->select('id', 'name')->get();
    }

    public function saveFiles(Request $request, Deal $deal)
    {
        /*if ($request->hasFile('file')) {
            $files = $request->file;
            foreach ($files as $file) {
                $type = $this->service->definitionCommentType($file);
                $space = SpaceService::getCurrentSpaceCode();
                $name = "/deal_$space/$deal->id/$type";
                $path = Storage::disk('public')->put($name, $file);
                DealComment::query()->create([
                    'type' => $type,
                    'content' => $path,
                    'deal_id' => $deal->id,
                    'author_id' => backpack_user()->id,
                ]);
            }
        }*/
    }
}
