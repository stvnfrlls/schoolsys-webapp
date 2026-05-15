<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'view users',
            'create users',
            'edit users',
            'delete users',
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            'view permissions',
            'create permissions',
            'edit permissions',
            'delete permissions',
            'view activity logs',
            'view grade levels',
            'create grade levels',
            'edit grade levels',
            'delete grade levels',
            'view sections',
            'create sections',
            'edit sections',
            'delete sections',
            'view subjects',
            'create subjects',
            'edit subjects',
            'delete subjects',
            'view subject per level',
            'create subject per level',
            'edit subject per level',
            'delete subject per level',
            'view students',
            'create students',
            'edit students',
            'delete students',
            'view enrollments',
            'create enrollments',
            'edit enrollments',
            'delete enrollments',
            'view school years',
            'create school years',
            'edit school years',
            'delete school years',
            'view faculty',
            'create faculty',
            'edit faculty',
            'delete faculty',
            'view schedules',
            'create schedules',
            'edit schedules',
            'delete schedules',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $roles = [
            'Admin' => $permissions,
            'Faculty' => [
                'view users',
                'view grade levels',
                'view sections',
                'view subjects',
                'view subject per level',
                'view students',
                'view enrollments',
                'view schedules',
                'view school years',
            ],
            'Student' => [
                'view schedules',
                'view enrollments',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($rolePermissions);
        }
    }
}
