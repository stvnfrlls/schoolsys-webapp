<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Users
            'view users',
            'create users',
            'edit users',
            'delete users',

            // Roles
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',

            // Permissions
            'view permissions',
            'create permissions',
            'edit permissions',
            'delete permissions',

            // Activity Logs
            'view activity logs',

            // Grade Levels
            'view grade levels',
            'create grade levels',
            'edit grade levels',
            'delete grade levels',

            // Sections
            'view sections',
            'create sections',
            'edit sections',
            'delete sections',

            // Subjects
            'view subjects',
            'create subjects',
            'edit subjects',
            'delete subjects',

            // Subject Per Level
            'view subject per level',
            'create subject per level',
            'edit subject per level',
            'delete subject per level',

            // Students
            'view students',
            'create students',
            'edit students',
            'delete students',

            // Enrollments
            'view enrollments',
            'create enrollments',
            'edit enrollments',
            'delete enrollments',

            // School Year
            'view school years',
            'create school years',
            'edit school years',
            'delete school years',

            // Faculty
            'view faculty',
            'create faculty',
            'edit faculty',
            'delete faculty',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $roles = [
            'Admin' => $permissions,
            'Faculty' => [
                'view users',
            ],
            'Student' => [],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($rolePermissions);
        }
    }
}
