<?php

namespace Database\Seeders;

use App\Enums\PermissionsEnum;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function scopesDisplayName($scope): string
    {
        $map = [
            'create' => '- создание',
            'delete' => '- удаление',
            'update'   => '- редактирование',
            'change_pipeline' => '- смена воронки',
            'change_stage' => '- смена стадии',
            'change_responsible' => '- смена ответственного',
            'change_responsible_self' => '- смена ответственного если менеджер ответственный',
            'list' => '- просмотр списка',
            'activate' => '- смена активности'
        ];
        return $map[$scope] ?? '';
    }

    public function scopes(): array
    {
        return array_merge(collect(PermissionsEnum::cases())->mapWithKeys(function (PermissionsEnum $item) {
            return [
                $item->name => [null]
            ];
        })->toArray(), [
            PermissionsEnum::deals->name => [
                'create',
                'delete',
                'update',
                'change_pipeline',
                'change_stage',
                'change_responsible',
                'change_responsible_self'
            ],
            PermissionsEnum::tasks->name     => [
                'create',
                'delete',
                'update',
                'change_stage',
                'change_responsible',
                'change_responsible_self',
            ],
            PermissionsEnum::clients->name   => [
                'create',
                'update',
                'delete',
                'list'
            ],
            PermissionsEnum::fields->name     => [
                'create',
                'activate',
                'update',
                'delete',
                'list'
            ],
            PermissionsEnum::deal_buttons->name   => [
                'create',
                'update',
                'delete',
                'list'
            ],
            PermissionsEnum::task_buttons->name   => [
                'create',
                'update',
                'delete',
                'list'
            ],
            PermissionsEnum::task_stages->name   => [
                'create',
                'update',
                'delete',
                'list'
            ],
            PermissionsEnum::pipelines->name   => [
                'create',
                'update',
                'delete',
                'list'
            ],
            PermissionsEnum::settings->name   => [
                'list'
            ],
        ]);
    }

    public function getScope(PermissionsEnum $permission): array
    {
        return $this->scopes()[$permission->name];
    }

    public function run()
    {
        $permissions = PermissionsEnum::cases();
        foreach ($permissions as $permission) {

            $scopes = $this->getScope($permission);
            $group = $permission->name;

            foreach ($scopes as $scope) {
                $name = ($scope && strlen($scope) > 0) ? trim("$permission->name.$scope") : $permission->name;
                $scopesDisplayName = $this->scopesDisplayName($scope);
                $value = ($scope && strlen($scope) > 0) ? trim("$permission->value $scopesDisplayName") : $permission->value;
                $pernisiionDB = Permission::where('name', $name)->first();
                if ($pernisiionDB) {
                    $pernisiionDB->group = $group;
                    $pernisiionDB->display_name = $value;
                    $pernisiionDB->save();
                } else {
                    Permission::query()->create([
                        'name'         => $name,
                        'display_name' => $value,
                        'guard_name'   => config('backpack.base.guard'),
                        'group'        => $group,
                    ]);
                }
            }
        }

        $role = Role::query()->where('name', 'admin')->first();

        if (!$role) {
            $role = Role::create([
                'name'       => 'admin',
                'guard_name' => config('backpack.base.guard')
            ]);
        }

        $permissionsDB = Permission::all();

        foreach ($permissionsDB as $permission) {
            $role->givePermissionTo($permission->name);
        }
    }
}
