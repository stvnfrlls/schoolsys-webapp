<?php

return [

    [
        'section' => 'Main',
        'items' => [
            [
                'label' => 'Dashboard',
                'route' => 'dashboard',
                'icon' => 'home',
                'permission' => null,
            ],
        ],
    ],

    [
        'section' => 'Academic',
        'collapsible' => true,
        'items' => [
            [
                'label' => 'Students',
                'route' => 'students.index',
                'permission' => 'view students',
                'child' => true,
            ],
            [
                'label' => 'Faculty',
                'route' => 'faculty.index',
                'permission' => 'view faculty',
                'child' => true,
            ],
            [
                'label' => 'Enrollment',
                'route' => 'enrollments.index',
                'permission' => 'view enrollments',
                'child' => true,
            ],
            [
                'label' => 'Schedule',
                'route' => 'schedules.index',
                'permission' => 'view schedules',
                'child' => true,
            ],
            [
                'label' => 'Grades',
                'route' => 'grades.index',
                'permission' => 'view grades',
                'child' => true,
            ],
            [
                'label' => 'Attendance',
                'route' => 'attendance.index',
                'permission' => 'view attendance',
                'child' => true,
            ],
        ],
    ],

    [
        'section' => 'Curriculum',
        'collapsible' => true,
        'items' => [
            [
                'label' => 'Grade Levels',
                'route' => 'gradelevels.index',
                'permission' => 'view grade levels',
                'child' => true,
            ],
            [
                'label' => 'Sections',
                'route' => 'sections.index',
                'permission' => 'view sections',
                'child' => true,
            ],
            [
                'label' => 'Subjects',
                'route' => 'subjects.index',
                'permission' => 'view subjects',
                'child' => true,
            ],
            [
                'label' => 'Subjects per Grade',
                'route' => 'subjectperlevel.index',
                'permission' => 'view subject per level',
                'child' => true,
            ],
            [
                'label' => 'School Years',
                'route' => 'schoolyears.index',
                'permission' => 'view school years',
                'child' => true,
            ],
        ],
    ],

    [
        'section' => 'System',
        'collapsible' => true,
        'items' => [
            [
                'label' => 'Users',
                'route' => 'users.index',
                'permission' => 'view users',
                'child' => true,
            ],
            [
                'label' => 'Roles',
                'route' => 'roles.index',
                'permission' => 'view roles',
                'child' => true,
            ],
            [
                'label' => 'Permissions',
                'route' => 'permissions.index',
                'permission' => 'view permissions',
                'child' => true,
            ],
            [
                'label' => 'Activity Logs',
                'route' => 'activitylogs.index',
                'permission' => 'view activity logs',
                'child' => true,
            ],
            [
                'label' => 'Settings',
                'route' => '#',
                'permission' => 'manage settings',
                'child' => true,
            ],
        ],
    ],

];
