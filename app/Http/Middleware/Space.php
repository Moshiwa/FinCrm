<?php

namespace App\Http\Middleware;

use App\Services\Space\SpaceService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Space
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */

    protected array $except = [
        'admin/space-change/*',
        'admin/edit-account-info',
        'admin/2fa',
        'admin/logout'
    ];

    public function handle(Request $request, Closure $next)
    {
        if($request->has('space')) {
            SpaceService::setCurrentSpaceCode($request->get('space'));
        }
        SpaceService::setDefaultDatabaseConnection();
        SpaceService::setDefaultUploadDiskPath();
        $user = Auth::user();
        if(!$this->inExceptArray($request) && $user && !$user->canAccessCurrentSpace()) {
            $availableSpaces = $user->availableSpaces();
            if($availableSpaces->count() > 0) {
                $firstAvailable = $availableSpaces->first();
                SpaceService::setCurrentSpaceCode($firstAvailable->code);
                return redirect(backpack_url('/'));
            }
            abort(403);
        }
        return $next($request);
    }
    /**
     * Determine if the request has a URI that should pass through CSRF verification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function inExceptArray($request)
    {
        foreach ($this->except as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if ($request->fullUrlIs($except) || $request->is($except)) {
                return true;
            }
        }

        return false;
    }
}
