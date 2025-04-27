<?php
header('Content-Type: application/json');

// Sample data for development
$departments = [
    [
        'id' => 1,
        'name' => 'IT',
        'manager' => 'Trần Thị B',
        'employee_count' => 10
    ],
    [
        'id' => 2,
        'name' => 'HR',
        'manager' => 'Nguyễn Văn A',
        'employee_count' => 5
    ],
    [
        'id' => 3,
        'name' => 'Finance',
        'manager' => 'Lê Văn C',
        'employee_count' => 8
    ]
];

echo json_encode([
    'success' => true,
    'data' => $departments
]); 