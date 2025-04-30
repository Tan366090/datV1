<?php
require_once __DIR__ . '/backend/src/config/database.php';
require_once __DIR__ . '/backend/src/models/User.php';
require_once __DIR__ . '/backend/src/models/UserProfile.php';

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "qlnhansu";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Kiểm tra số lượng nhân viên active
    $sql = "SELECT COUNT(*) as total FROM employees WHERE status = 'active'";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $totalEmployees = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Kiểm tra dữ liệu chấm công hôm nay
    $sql = "SELECT * FROM attendance WHERE DATE(attendance_date) = CURDATE()";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $todayAttendance = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Kiểm tra dữ liệu mẫu
    $sql = "SELECT * FROM attendance LIMIT 5";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $sampleData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Tổng số nhân viên active: " . $totalEmployees . "\n\n";
    echo "Dữ liệu chấm công hôm nay:\n";
    print_r($todayAttendance);
    echo "\nDữ liệu mẫu:\n";
    print_r($sampleData);
    
} catch(PDOException $e) {
    echo "Lỗi: " . $e->getMessage();
}

$conn = null;

try {
    // Initialize models
    $userModel = new User();
    $profileModel = new UserProfile();
    
    // Check users
    $users = $userModel->getAll();
    echo "Total users: " . count($users) . "\n";
    if (count($users) > 0) {
        echo "Sample users:\n";
        foreach (array_slice($users, 0, 3) as $user) {
            echo "- User ID: {$user['user_id']}, Username: {$user['username']}, Email: {$user['email']}\n";
        }
    }
    
    // Check profiles
    $profiles = $profileModel->getAll();
    echo "\nTotal profiles: " . count($profiles) . "\n";
    if (count($profiles) > 0) {
        echo "Sample profiles:\n";
        foreach (array_slice($profiles, 0, 3) as $profile) {
            echo "- Profile ID: {$profile['profile_id']}, User ID: {$profile['user_id']}, Full Name: {$profile['full_name']}\n";
        }
    }
    
    // Check if all users have profiles
    $usersWithoutProfiles = [];
    foreach ($users as $user) {
        $profile = $profileModel->getProfileByUserId($user['user_id']);
        if (!$profile) {
            $usersWithoutProfiles[] = $user['username'];
        }
    }
    
    if (!empty($usersWithoutProfiles)) {
        echo "\nUsers without profiles:\n";
        foreach ($usersWithoutProfiles as $username) {
            echo "- $username\n";
        }
    } else {
        echo "\nAll users have profiles.\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 