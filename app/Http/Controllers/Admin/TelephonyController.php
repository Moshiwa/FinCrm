<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TelephonyRequest;
use App\Services\Telephony\Uiscom\UiscomService;

class TelephonyController extends Controller
{
    public function call(TelephonyRequest $request)
    {
        $data = $request->validated();
        $service = new UiscomService();

        $phone = $service->cleanPhone($data['phone']);
        $error = $service->getError();

        if (!empty($phone) && empty($error)) {
            $service->call($data['phone']);
        }

        return response()->json([
            'errors' => [ $error ]
        ]);
    }
}
