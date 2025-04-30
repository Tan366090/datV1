<?php
header('Content-Type: application/json');
require_once '../../config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();

    // 1. Hiệu suất nhân viên (Performance Chart)
    $performanceQuery = "SELECT 
        QUARTER(created_at) as quarter,
        AVG(score) as avg_score
        FROM performances 
        WHERE YEAR(created_at) = YEAR(CURRENT_DATE)
        GROUP BY QUARTER(created_at)
        ORDER BY quarter";
    $performanceStmt = $conn->prepare($performanceQuery);
    $performanceStmt->execute();
    $performanceData = $performanceStmt->fetchAll(PDO::FETCH_ASSOC);

    // 2. Chi phí lương (Salary Chart)
    $salaryQuery = "SELECT 
        DATE_FORMAT(payment_date, '%Y-%m') as month,
        SUM(net_salary) as total_salary
        FROM payroll 
        WHERE YEAR(payment_date) = YEAR(CURRENT_DATE)
        GROUP BY DATE_FORMAT(payment_date, '%Y-%m')
        ORDER BY month";
    $salaryStmt = $conn->prepare($salaryQuery);
    $salaryStmt->execute();
    $salaryData = $salaryStmt->fetchAll(PDO::FETCH_ASSOC);

    // 3. Thống kê nghỉ phép (Leave Chart)
    $leaveQuery = "SELECT 
        leave_type,
        COUNT(*) as count
        FROM leaves 
        WHERE YEAR(start_date) = YEAR(CURRENT_DATE)
        GROUP BY leave_type";
    $leaveStmt = $conn->prepare($leaveQuery);
    $leaveStmt->execute();
    $leaveData = $leaveStmt->fetchAll(PDO::FETCH_ASSOC);

    // 4. Tuyển dụng (Recruitment Chart)
    $recruitmentQuery = "SELECT 
        status,
        COUNT(*) as count
        FROM job_applications 
        WHERE YEAR(created_at) = YEAR(CURRENT_DATE)
        GROUP BY status";
    $recruitmentStmt = $conn->prepare($recruitmentQuery);
    $recruitmentStmt->execute();
    $recruitmentData = $recruitmentStmt->fetchAll(PDO::FETCH_ASSOC);

    // 5. Đào tạo (Training Chart)
    $trainingQuery = "SELECT 
        tc.category,
        COUNT(tr.id) as participant_count
        FROM training_courses tc
        LEFT JOIN training_registrations tr ON tc.id = tr.course_id
        WHERE YEAR(tc.start_date) = YEAR(CURRENT_DATE)
        GROUP BY tc.category";
    $trainingStmt = $conn->prepare($trainingQuery);
    $trainingStmt->execute();
    $trainingData = $trainingStmt->fetchAll(PDO::FETCH_ASSOC);

    // 6. Quản lý tài sản (Assets Chart)
    $assetsQuery = "SELECT 
        status,
        COUNT(*) as count
        FROM assets 
        GROUP BY status";
    $assetsStmt = $conn->prepare($assetsQuery);
    $assetsStmt->execute();
    $assetsData = $assetsStmt->fetchAll(PDO::FETCH_ASSOC);

    // Format data for charts
    $response = [
        'success' => true,
        'data' => [
            'performance' => $performanceData,
            'salary' => $salaryData,
            'leaves' => $leaveData,
            'recruitment' => $recruitmentData,
            'training' => $trainingData,
            'assets' => $assetsData
        ]
    ];

    echo json_encode($response);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?> 