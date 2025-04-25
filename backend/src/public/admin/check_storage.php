<?php
// Bật báo lỗi chi tiết
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

try {
    // Kiểm tra xem file config có tồn tại không
    $configPath = __DIR__ . '/../../config/database.php';
    if (!file_exists($configPath)) {
        throw new Exception("Database configuration file not found at: " . $configPath);
    }

    require_once $configPath;

    // Kiểm tra các thông số kết nối
    if (!isset($dbConfig['host']) || !isset($dbConfig['database']) || !isset($dbConfig['username'])) {
        throw new Exception("Database configuration is incomplete");
    }

    // Kết nối database
    $dsn = "mysql:host={$dbConfig['host']};dbname={$dbConfig['database']};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ];

    $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], $options);

    // Kiểm tra kết nối
    $pdo->query("SELECT 1");

    // Lấy danh sách tất cả các bảng
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    
    if (empty($tables)) {
        throw new Exception("No tables found in database");
    }

    $result = [];
    foreach ($tables as $table) {
        try {
            // Đếm số lượng bản ghi trong mỗi bảng
            $count = $pdo->query("SELECT COUNT(*) as count FROM `$table`")->fetch()['count'];
            
            // Lấy 5 bản ghi đầu tiên làm mẫu
            $sample = $pdo->query("SELECT * FROM `$table` LIMIT 5")->fetchAll();
            
            $result[$table] = [
                'count' => $count,
                'sample' => $sample
            ];
        } catch (Exception $e) {
            $result[$table] = [
                'error' => "Error accessing table: " . $e->getMessage()
            ];
        }
    }

    echo json_encode([
        'success' => true,
        'data' => $result
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}
?> 