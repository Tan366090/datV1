<?php
header('Content-Type: application/json');

// Sample data for development
$activities = [
    [
        'id' => 1,
        'type' => 'login',
        'user' => 'Nguyễn Văn A',
        'time' => '2024-04-20 08:30:00',
        'details' => 'Đăng nhập vào hệ thống'
    ],
    [
        'id' => 2,
        'type' => 'update',
        'user' => 'Trần Thị B',
        'time' => '2024-04-20 09:15:00',
        'details' => 'Cập nhật thông tin nhân viên'
    ],
    [
        'id' => 3,
        'type' => 'create',
        'user' => 'Lê Văn C',
        'time' => '2024-04-20 10:00:00',
        'details' => 'Tạo báo cáo mới'
    ]
];

echo json_encode([
    'success' => true,
    'data' => $activities
]); 