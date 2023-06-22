<?php

namespace App\Helpers;

use App\Services\Space\SpaceService;
use Illuminate\Support\Facades\DB;

class RequestHelper
{
    /**
     * Не даем менять роли из других организаций
     * Для этого добавляем или удалем их в реквесте
     *
     * @param $request
     * @return mixed
     */
    public static function updateRequestRoles($request)
    {
        $space = SpaceService::getCurrentSpace();
        $user_id = self::getUserIdFromRequest($request);
        $old_data = DB::table('model_has_roles')
            ->where('model_type', 'App\Models\User')
            ->where('model_id', $user_id)
            ->get();


        $all_roles = [];
        foreach ($old_data as $old_role) {
            $all_roles[$old_role->role_id] = $old_role->role_id;
        }

        $space_roles = [];
        foreach ($space->roles as $space_role) {
            $space_roles[] = $space_role->id;
            $all_roles[$space_role->id] = $space_role->id;
        }

        $save_roles = [];
        $parameters = $request->all();
        foreach ($parameters['roles'] ?? []  as $role) {
            $save_roles[] = $role;
            $all_roles[(int)$role] = (int)$role;
        }

        $delete_role = [];
        foreach ($space_roles as $space_role) {
            foreach ($save_roles as $save_role) {
                if ($space_role != $save_role) {
                    $delete_role[] = $space_role;
                }
            }
        }

        $save = [];
        foreach ($all_roles as $all_role_id) {
            foreach ($delete_role as $delete_role_id) {
                if ($all_role_id !== $delete_role_id) {
                    $save[] = $all_role_id;
                }
            }
        }

        $parameters['roles'] = [];
        foreach ($save as $item) {
            $parameters['roles'][] = $item;
        }

        $parameters['roles'] = array_unique($parameters['roles']);
        $request->merge(['roles' => $parameters['roles']]);

        return $request;
    }

    private static function getUserIdFromRequest($request): ?int
    {
        $path = $request->getPathInfo();
        $parts = explode('/', $path);
        foreach ($parts as $part) {
            if (is_numeric($part)) {
                return (int) $part;
            }
        }

        return null;
    }
}
