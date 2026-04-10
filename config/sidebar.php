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
                'route' => 'enrollment.index',
                'permission' => 'view enrollment',
                'child' => true,
            ],
            [
                'label' => 'Schedule',
                'route' => 'schedule.index',
                'permission' => 'view schedule',
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
                'route' => '#',
                'permission' => 'view grade levels',
                'child' => true,
            ],
            [
                'label' => 'Sections',
                'route' => '#',
                'permission' => 'view sections',
                'child' => true,
            ],
            [
                'label' => 'Subjects',
                'route' => '#',
                'permission' => 'view subjects',
                'child' => true,
            ],
            [
                'label' => 'Subjects per Grade',
                'route' => '#',
                'permission' => 'view subject assignments',
                'child' => true,
            ],
            [
                'label' => 'School Years',
                'route' => '#',
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
                'route' => '#',
                'permission' => 'view users',
                'child' => true,
            ],
            [
                'label' => 'Roles',
                'route' => '#',
                'permission' => 'view roles',
                'child' => true,
            ],
            [
                'label' => 'Permissions',
                'route' => '#',
                'permission' => 'view permissions',
                'child' => true,
            ],
            [
                'label' => 'Activity Logs',
                'route' => '#',
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
