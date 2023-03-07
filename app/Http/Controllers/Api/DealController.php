<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DealRequest;
use App\Http\Resources\DealResource;
use App\Models\Deal;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DealController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $deals = Deal::query()->get();

        return DealResource::collection($deals);
    }

    public function show(Request $request, Deal $deal): DealResource
    {
        return DealResource::make($deal);
    }
}
