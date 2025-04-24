<?php
return [
    // Auth routes
    [
        'method' => 'POST',
        'path' => '/auth/login',
        'controller' => 'App\\Controllers\\AuthController',
        'method' => 'login',
        'validation' => [
            'json' => [
                'username' => ['type' => 'string', 'min_length' => 3],
                'password' => ['type' => 'string', 'min_length' => 6]
            ]
        ]
    ],
    [
        'method' => 'POST',
        'path' => '/auth/logout',
        'controller' => 'App\\Controllers\\AuthController',
        'method' => 'logout',
        'auth' => true
    ],
    
    // Employee routes
    [
        'method' => 'GET',
        'path' => '/employees',
        'controller' => 'App\\Controllers\\EmployeeController',
        'method' => 'index',
        'auth' => true
    ],
    [
        'method' => 'POST',
        'path' => '/employees',
        'controller' => 'App\\Controllers\\EmployeeController',
        'method' => 'store',
        'auth' => true,
        'validation' => [
            'json' => [
                'name' => ['type' => 'string', 'min_length' => 2],
                'email' => ['type' => 'email'],
                'department_id' => ['type' => 'integer'],
                'position_id' => ['type' => 'integer']
            ]
        ]
    ],
    
    // Department routes
    [
        'method' => 'GET',
        'path' => '/departments',
        'controller' => 'App\\Controllers\\DepartmentController',
        'method' => 'index',
        'auth' => true
    ],
    
    // Position routes
    [
        'method' => 'GET',
        'path' => '/positions',
        'controller' => 'App\\Controllers\\PositionController',
        'method' => 'index',
        'auth' => true
    ],
    
    // Attendance routes
    [
        'method' => 'POST',
        'path' => '/attendance/check-in',
        'controller' => 'App\\Controllers\\AttendanceController',
        'method' => 'checkIn',
        'auth' => true
    ],
    [
        'method' => 'POST',
        'path' => '/attendance/check-out',
        'controller' => 'App\\Controllers\\AttendanceController',
        'method' => 'checkOut',
        'auth' => true
    ],
    
    // Salary routes
    [
        'method' => 'GET',
        'path' => '/salaries',
        'controller' => 'App\\Controllers\\SalaryController',
        'method' => 'index',
        'auth' => true
    ],
    
    // Document routes
    [
        'method' => 'GET',
        'path' => '/documents',
        'controller' => 'App\\Controllers\\DocumentController',
        'method' => 'index',
        'auth' => true
    ],
    [
        'method' => 'POST',
        'path' => '/documents',
        'controller' => 'App\\Controllers\\DocumentController',
        'method' => 'store',
        'auth' => true
    ]
]; 