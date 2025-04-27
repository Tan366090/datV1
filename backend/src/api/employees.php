<?php
header('Content-Type: application/json');

// Sample data for development
$employees = [
    [
        'id' => 1,
        'name' => 'Nguyễn Văn A',
        'department' => 'IT',
        'position' => 'Developer',
        'status' => 'active'
    ],
    [
        'id' => 2,
        'name' => 'Trần Thị B',
        'department' => 'HR',
        'position' => 'Manager',
        'status' => 'active'
    ],
    [
        'id' => 3,
        'name' => 'Lê Văn C',
        'department' => 'Finance',
        'position' => 'Accountant',
        'status' => 'active'
    ]
];

echo json_encode([
    'success' => true,
    'data' => $employees
]); 