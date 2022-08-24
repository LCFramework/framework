<?php

namespace LCFramework\Framework\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $schema = $this->schema();

        $permissions = $this->getPermissions($schema);
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        foreach ($schema as $role) {
            Role::create(collect($role)->except(['permissions'])->all())
                ->givePermissionTo($role['permissions']);
        }
    }

    protected function schema(): array
    {
        return [
            [
                'name' => 'User',
                'permissions' => []
            ],
            [
                'name' => 'Administrator',
                'permissions' => [
                    'view admin'
                ]
            ]
        ];
    }

    protected function getPermissions(array $schema): array
    {
        $permissions = [];

        foreach ($schema as $role) {
            foreach ($role['permissions'] as $permission) {
                $permissions[] = $permission;
            }
        }

        return array_unique($permissions);
    }
}
