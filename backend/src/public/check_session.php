<?php
session_start();

// Function to check if the user is authenticated
function isAuthenticated() {
    return isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true;
}

// Function to check if required session variables are set
function hasRequiredSessionValues() {
    return isset($_SESSION['user_id'], $_SESSION['role']);
}

// Function to validate session expiration
function isSessionValid() {
    $sessionTimeout = 3600; // 1 hour
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $sessionTimeout)) {
        session_unset();
        session_destroy();
        return false;
    }
    $_SESSION['last_activity'] = time(); // Update last activity timestamp
    return true;
}

// Check all conditions
if (isAuthenticated() && hasRequiredSessionValues() && isSessionValid()) {
    // Prepare user data
    $user = [
        'user_id' => $_SESSION['user_id'],
        'role' => $_SESSION['role'],
        'username' => $_SESSION['username'] ?? '',
        'full_name' => $_SESSION['full_name'] ?? '',
        'email' => $_SESSION['email'] ?? ''
    ];

    // Determine dashboard URL based on role
    $dashboardUrl = '';
    switch (strtolower($user['role'])) {
        case 'admin':
            $dashboardUrl = '/qlnhansu_V2/backend/src/public/admin/dashboard_admin.html';
            break;
        case 'manager':
            $dashboardUrl = '/qlnhansu_V2/backend/src/public/manager/dashboard.html';
            break;
        case 'employee':
            $dashboardUrl = '/qlnhansu_V2/backend/src/public/employee/dashboard.html';
            break;
        case 'hr':
            $dashboardUrl = '/qlnhansu_V2/backend/src/public/hr/dashboard.html';
            break;
        default:
            $dashboardUrl = '/qlnhansu_V2/backend/src/public/login_new.html';
            break;
    }

    // Return session data
    echo json_encode([
        'authenticated' => true,
        'user' => $user,
        'dashboardUrl' => $dashboardUrl
    ], JSON_UNESCAPED_UNICODE);
} else {
    // Redirect to login page if any condition fails
    echo json_encode([
        'authenticated' => false,
        'redirectUrl' => '/qlnhansu_V2/backend/src/public/login_new.html'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
?>