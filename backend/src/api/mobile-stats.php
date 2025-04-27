<?php
header('Content-Type: application/json');

// Sample data for development
$stats = [
    'totalEmployees' => 50,
    'activeEmployees' => 45,
    'todayAttendance' => '90%',
    'onLeave' => 5,
    'departments' => [
        'IT' => 15,
        'HR' => 10,
        'Finance' => 8,
        'Marketing' => 7,
        'Sales' => 10
    ],
    'attendanceTrend' => [
        'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'],
        'data' => [85, 90, 88, 92, 90]
    ]
];

echo json_encode([
    'success' => true,
    'data' => $stats
]); 