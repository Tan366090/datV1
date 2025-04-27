<?php
header('Content-Type: application/json');

// Sample data for development
$performances = [
    [
        'id' => 1,
        'employee_id' => 1,
        'employee_name' => 'Nguyễn Văn A',
        'score' => 85,
        'rating' => 'Tốt',
        'period' => '2024-04'
    ],
    [
        'id' => 2,
        'employee_id' => 2,
        'employee_name' => 'Trần Thị B',
        'score' => 92,
        'rating' => 'Xuất sắc',
        'period' => '2024-04'
    ],
    [
        'id' => 3,
        'employee_id' => 3,
        'employee_name' => 'Lê Văn C',
        'score' => 78,
        'rating' => 'Khá',
        'period' => '2024-04'
    ]
];

echo json_encode([
    'success' => true,
    'data' => $performances
]); 