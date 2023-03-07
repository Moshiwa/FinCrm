<?php

namespace App\Http\Middleware;

use App\Services\Space\SpaceService;
use Closure;
use Illuminate\Http\Request;

class SpaceApi
{
    public function handle(Request $request, Closure $next)
    {
        // Проверяем указан ли код организации
        $space_code = $request->headers->get('X-Space');
        if(! $space_code) {
            abort(400, 'The organization is not specified');
        }

        // Проверяем существует ли организация с таким кодом
        $spaces = $request->user()->spaces()->pluck('name', 'code')->toArray();
        if (! array_key_exists($space_code, $spaces)) {
            abort(403, 'Organization not found');
        }

        // Устанавливаем данную организацию по умолчанию
        SpaceService::setCurrentSpaceCode($space_code);

        // Проверяем есть ли разрешение на использование API
        /*if (!$request->user()->can('api')) {
            abort(403, 'Permission to use the API was not found');
        }*/

        return $next($request);
    }
}
