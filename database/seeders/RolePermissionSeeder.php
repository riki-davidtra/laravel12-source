<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // // Reset cache
        // app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // // // Create Permission
        // $entities = [
        //     'profiles'    => 'web',
        //     'users'       => 'web',
        //     'roles'       => 'web',
        //     'permissions' => 'web',
        //     'settings'    => 'web'
        // ];
        // $actions = ['index', 'create', 'edit', 'delete'];
        // foreach ($entities as $entity => $guard) {
        //     foreach ($actions as $action) {
        //         $permissions[] = [
        //             'name'       => "{$entity} {$action}",
        //             'guard_name' => $guard
        //         ];
        //     }
        // }
        // foreach ($permissions as $permission) {
        //     Permission::firstOrCreate($permission);
        // }

        // // Create Role
        // $superAdmin = Role::firstOrCreate(['name' => 'super admin']);
        // $admin      = Role::firstOrCreate(['name' => 'admin']);
        // $user       = Role::firstOrCreate(['name' => 'user']);

        // // Assign Permission
        // $superAdmin->givePermissionTo(Permission::all());
        // $admin->givePermissionTo(Permission::all());
        // $userPermissions = [
        //     'profiles index',
        //     'profiles edit',
        // ];
        // $user->givePermissionTo($userPermissions);





        // Reset cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create roles
        $RoleSuperAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $RoleAdmin      = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $RoleUser       = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);

        // get permissions 
        $entities = [
            'profiles'    => 'web',
            'users'       => 'web',
            'roles'       => 'web',
            'permissions' => 'web',
            'settings'    => 'web'
        ];
        $actions = ['index', 'create', 'edit', 'delete'];
        foreach ($entities as $entity => $guard) {
            foreach ($actions as $action) {
                Permission::firstOrCreate([
                    'name'       => "{$entity} {$action}",
                    'guard_name' => $guard
                ]);
            }
        }

        // $permissions     = Permission::pluck('name')->toArray();
        $permissions     = Permission::all();
        $userPermissions = [
            'profiles index',
            'profiles create',
            'profiles edit',
            'profiles delete',
        ];

        // set permissions for role 
        $RoleSuperAdmin->syncPermissions($permissions);
        $RoleAdmin->syncPermissions($permissions);
        $RoleUser->syncPermissions($userPermissions);

        // set role for users
        $roles = [
            'superadmin' => 'Super Admin',
            'admin'      => 'admin',
            'user'       => 'user',
        ];
        foreach ($roles as $username => $role) {
            $user = User::where('username', $username)->first();
            if ($user) {
                $user->assignRole($role);
            }
        }
    }
}
