<?php

namespace App\Services\Space;

use App\Models\Space;
use Illuminate\Database\Eloquent\Builder;

class SpaceService
{
    public static string $default_space_code = 'main';
    public static string $session_name = 'space';

    public static function getCurrentSpace()
    {
        $current_space_code = self::getCurrentSpaceCode();
        $current_space_code = empty($current_space_code) ? self::$default_space_code : $current_space_code;
        return Space::query()
            ->where('code', $current_space_code)
            ->first();
    }

    public static function getCurrentSpaceCode()
    {
        return session()->get(self::$session_name);
    }

    public static function setCurrentSpaceCode($code)
    {
        session()->put(self::$session_name, $code);
    }
}
