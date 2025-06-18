<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // // Create Permission
        // $permissions = [
        //     ['name' => 'profiles index',  'guard_name' => 'web'],
        //     ['name' => 'profiles create', 'guard_name' => 'web'],
        //     ['name' => 'profiles edit',   'guard_name' => 'web'],
        //     ['name' => 'profiles delete', 'guard_name' => 'web'],
        // ];
        // foreach ($permissions as $permission) {
        //     Permission::firstOrCreate($permission);
        // }

        $entities = [
            'profiles' => 'web',
            'users' => 'web',
            'roles' => 'web',
            'permissions' => 'web',
            'settings' => 'api'
        ];
        $actions = ['index', 'create', 'edit', 'delete'];
        foreach ($entities as $entity => $guard) {
            foreach ($actions as $action) {
                $permissions[] = [
                    'name' => "{$entity} {$action}",
                    'guard_name' => $guard
                ];
            }
        }

        // Create Role
        $superAdmin = Role::firstOrCreate(['name' => 'super admin']);
        $admin      = Role::firstOrCreate(['name' => 'admin']);
        $user       = Role::firstOrCreate(['name' => 'user']);

        // Assign Permission
        $superAdmin->givePermissionTo(Permission::all());
        $admin->givePermissionTo(Permission::all());
        $userPermissions = [
            'profiles index',
            'profiles edit',
        ];
        $user->givePermissionTo($userPermissions);
    }
}
