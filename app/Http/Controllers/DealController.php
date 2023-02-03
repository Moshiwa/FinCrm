<?php

namespace App\Http\Controllers;

use App\Http\Requests\DealRequest;
use App\Models\Deal;

class DealController extends Controller
{
    public function update(DealRequest $request)
    {
        $data = $request->validated();

        $deal = Deal::query()->find($data['id']);
        $deal->update($data);

        $deal->client()->update($data['client']);


    }
}
