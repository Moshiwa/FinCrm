<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CommentTypeEnum;
use App\Events\WebhookCommentPush;
use App\Http\Controllers\Controller;
use App\Http\Requests\TelephonyRequest;
use App\Models\Comment;
use App\Models\Deal;
use App\Models\User;
use App\Services\Telephony\Uiscom\UiscomService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TelephonyController extends Controller
{
    public function recordFromWebhook(Request $request)
    {
        $call_session_id = $request->get('call_session_id');
        $contact_phone_number = $request->get('contact_phone_number');
        $employee_id = $request->get('employee_id');
        $link = $request->get('file_link');
        $comment = Comment::query()->where('temp_id', $call_session_id)->first();
        $user = User::query()->where('uiscom_employee_id', $employee_id)->first();

        $comment?->update([
            'title' => 'Звонок на ' . $contact_phone_number,
            'temp_id' => null,
            'content' => $link,
            'author_id' => $user->id,
        ]);

        if ($comment->commentable_type === 'App\Models\Deal') {
            Cache::set("webhook.record.user{$comment->author_id}.deal{$comment->commentable_id}", $comment->id);
        }

        //broadcast(new WebhookCommentPush($comment));
    }

    public function check(Request $request)
    {
        $user_id = $request->get('user');
        $deal_id = $request->get('deal');
        $key = "webhook.record.user{$user_id}.deal{$deal_id}";
        $check = Cache::get($key);

        if (empty($check)) {
            return response()->json([
                'success' => false
            ]);
        }

        Cache::set($key, null);

        $comment = Comment::query()->find($check);
        return response()->json([
            'success' => true,
            'data' => $comment
        ]);
    }

    public function call(TelephonyRequest $request)
    {
        $data = $request->validated();
        $service = new UiscomService();

        $phone = $service->cleanPhone($data['phone']);
        $error = $service->getError();
        $success = false;

        if (!empty($phone) && empty($error)) {
            $call_id = $service->call($data['phone']);
            $error = $service->getError();
            if ($call_id && empty($error)) {
                $deal = Deal::query()->find($data['deal_id']);
                $deal->comments()->create([
                    'type' => CommentTypeEnum::AUDIO->value,
                    'author_id' => backpack_user()->id,
                    'content' => "Не удалось получить запись звонка (ID:$call_id)",
                    'title' => 'Звонок на ' . $phone,
                    'temp_id' => $call_id
                ]);

                $success = true;
            }
        }

        return response()->json([
            'success' => $success,
            'errors' => [ $error ]
        ]);
    }
}
