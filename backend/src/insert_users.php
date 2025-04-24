<?php
require_once 'api/config.php';

try {
    // Prepare the SQL statement
    $sql = "INSERT INTO users (name, department_id, role_id, status, join_date) VALUES 
    ('Nguyễn Văn A', 1, 2, 'active', '2023-01-15'),
    ('Trần Thị B', 1, 2, 'active', '2023-02-20'),
    ('Lê Văn C', 2, 2, 'active', '2023-03-10'),
    ('Phạm Thị D', 2, 2, 'active', '2023-04-05'),
    ('Hoàng Văn E', 3, 2, 'active', '2023-05-12'),
    ('Đỗ Thị F', 3, 2, 'active', '2023-06-18'),
    ('Vũ Văn G', 4, 2, 'active', '2023-07-22'),
    ('Bùi Thị H', 4, 2, 'active', '2023-08-30'),
    ('Đặng Văn I', 5, 2, 'active', '2023-09-14'),
    ('Ngô Thị K', 5, 2, 'active', '2023-10-25')";

    // Execute the query
    $conn->exec($sql);
    echo "Successfully inserted sample user data.\n";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?> 