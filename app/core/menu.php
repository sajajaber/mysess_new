<?php

// base plan only, not fix yet - Saja 18/5

// this file is used to store the menu items for each role, so instead of writing the menu items in each view, we just call this file and loop through the menu items to generate the sidebar

return [
    'nurse' => [
        'dashboard' => '/nurse/dashboard',
        'students' => '/nurse/students',
        'health-records' => '/nurse/health-records',
        'medications' => '/nurse/medications',
        'messages' => '/nurse/messages'
    ],
    'admin' => [
        'dashboard' => '/admin/dashboard',
        'users' => '/admin/users',
        'students' => '/admin/students',
        'sessions' => '/admin/sessions',
        'reports' => '/admin/reports',
        'messages' => '/admin/messages'
    ],
    'parent' => [
        'dashboard' => '/parent/dashboard',
        'academic' => '/parent/academic-records',
        'health' => '/parent/health-records',
        'messages' => '/parent/messages'
    ],
    'teacher' => [
        'dashboard' => '/teacher/dashboard',
        'students' => '/teacher/students',
        'sessions' => '/teacher/sessions',
        'progress reports' => '/teacher/progress-reports',
        'messages' => '/teacher/messages'
    ],
    'therapist' => [
        'dashboard' => '/therapist/dashboard',
        'students' => '/therapist/students',
        'iep goals' => '/therapist/iep-goals',
        'sessions' => '/therapist/sessions',
        'messages' => '/therapist/messages'
    ],
    'boarding-staff' => [
        'dashboard' => '/boarding-staff/dashboard',
        'students' => '/boarding-staff/students',
        'daily logs' => '/boarding-staff/daily-logs',
        'activities' => '/boarding-staff/activities'
    ]
];