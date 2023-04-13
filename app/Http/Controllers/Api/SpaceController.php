<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SpaceResource;
use App\Models\Space;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SpaceController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        if (backpack_user()->hasRole('admin')) {
            return SpaceResource::collection(Space::query()->get());
        }

        return SpaceResource::collection(backpack_user()->spaces);
    }
}
