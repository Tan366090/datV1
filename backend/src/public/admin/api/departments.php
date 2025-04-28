<?php
header('Content-Type: application/json');

try {
    // Kết nối database
    $db = new PDO('mysql:host=localhost;dbname=qlnhansu', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Lấy dữ liệu phòng ban và số lượng nhân viên
    $query = "SELECT 
                d.department_id,
                d.name,
                COUNT(e.employee_id) as employee_count
              FROM departments d
              LEFT JOIN employees e ON d.department_id = e.department_id
              GROUP BY d.department_id, d.name
              ORDER BY employee_count DESC";

    $stmt = $db->prepare($query);
    $stmt->execute();
    $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Trả về dữ liệu dưới dạng JSON
    echo json_encode([
        'success' => true,
        'data' => $departments
    ]);

} catch (PDOException $e) {
    // Xử lý lỗi
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?> 