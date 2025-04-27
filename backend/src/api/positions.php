<?php
header('Content-Type: application/json');

// Sample data for development
$positions = [
    [
        'id' => 1,
        'name' => 'Developer',
        'department' => 'IT',
        'level' => 'Senior'
    ],
    [
        'id' => 2,
        'name' => 'Manager',
        'department' => 'HR',
        'level' => 'Manager'
    ],
    [
        'id' => 3,
        'name' => 'Accountant',
        'department' => 'Finance',
        'level' => 'Junior'
    ]
];

echo json_encode([
    'success' => true,
    'data' => $positions
]); 