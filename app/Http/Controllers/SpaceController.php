<?php

namespace App\Http\Controllers;

use App\Models\Space;
use App\Services\Space\SpaceService;
use Illuminate\Http\RedirectResponse;

class SpaceController extends Controller
{
    public function spaceChange($code): RedirectResponse
    {
        $space = Space::query()->where('code', $code);
        SpaceService::enableSpace($space);

        return redirect()->back();
    }
}
