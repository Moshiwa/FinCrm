<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TelephonyRequest;
use App\Services\Telephony\Uiscom\UiscomService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelephonyController extends Controller
{
    public function recordFromWebhook(Request $request)
    {



        $link = $request->get('file_link');
        $employee_full_name = $request->get('employee_full_name');
        Log::info('WEBHOOK:' . json_encode($request->query()));
    }

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
