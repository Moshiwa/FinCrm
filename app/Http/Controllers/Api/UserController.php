<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DealRequest;
use App\Http\Resources\DealResource;
use App\Http\Resources\UserResource;
use App\Models\Deal;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $with = $request->get('with', []);

        $users = User::query()
            ->with($with)
            ->get();

        return UserResource::collection($users);
    }

    public function show(Request $request, User $user)
    {
        $with = $request->get('with', []);
        $user->load($with);

        return UserResource::make($user);
    }
}
