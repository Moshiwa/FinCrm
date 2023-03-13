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
            'edit'   => 'редактирование',
            'create' => 'создание',
            'delete' => 'удаление',
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
                'edit',
                'delete'
            ],
            PermissionsEnum::tasks->name     => [
                'create',
                'edit',
                'delete'
            ],
            PermissionsEnum::clients->name   => [
                'create',
                'edit',
                'delete'
            ],
            PermissionsEnum::fields->name     => [
                'create',
                'edit',
                'delete'
            ],
            PermissionsEnum::deal_buttons->name   => [
                'create',
                'edit',
                'delete'
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
