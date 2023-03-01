<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DealRequest;
use App\Http\Resources\DealResource;
use App\Models\Deal;
use Illuminate\Http\Request;

class DealController extends Controller
{
    public function index(Request $request)
    {
        $with = $request->get('with', 'fields');

        $deals = Deal::query()
            ->with($with)
            ->get();

        return DealResource::collection($deals);
    }

    public function show(Request $request, Deal $deal)
    {
        $with = $request->get('with', 'fields');
        $deal->load($with);

        return DealResource::make($deal);
    }

    public function store(DealRequest $request, Deal $client)
    {

    }
}
