<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DealRequest;
use App\Http\Resources\DealResource;
use App\Http\Resources\UserResource;
use App\Models\Deal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $users = User::query()->get();

        return UserResource::collection($users);
    }

    public function show(Request $request, User $user): UserResource
    {
        return UserResource::make($user);
    }
}
