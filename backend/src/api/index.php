<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

// Set headers for JSON response and CORS
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Max-Age: 3600');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Load required files
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/middleware/AuthMiddleware.php';
require_once __DIR__ . '/middleware/RequestValidator.php';
require_once __DIR__ . '/utils/ResponseHandler.php';

// Initialize database connection
$db = Database::getInstance();
$conn = $db->getConnection();

// Initialize middleware
$authMiddleware = new AuthMiddleware();
$requestValidator = new RequestValidator();

// Get the request path
$request_uri = $_SERVER['REQUEST_URI'];
$base_path = '/qlnhansu_V2/backend/src/api/';
$path = str_replace($base_path, '', $request_uri);
$path = trim($path, '/');

// Log the request for debugging
error_log("Request URI: " . $request_uri);
error_log("Base Path: " . $base_path);
error_log("Path: " . $path);

// Load routes
$routes = require_once __DIR__ . '/routes/routes.php';

// Find matching route
$matchedRoute = null;
foreach ($routes as $route) {
    if ($route['method'] === $_SERVER['REQUEST_METHOD'] && $route['path'] === $path) {
        $matchedRoute = $route;
        break;
    }
}

// Handle route not found
if (!$matchedRoute) {
    ResponseHandler::sendError('Route not found', 404);
    exit();
}

// Apply middleware
try {
    // Validate request
    $requestValidator->validate($matchedRoute['validation'] ?? []);
    
    // Check authentication if required
    if ($matchedRoute['auth'] ?? false) {
        $authMiddleware->authenticate();
    }
    
    // Load and execute controller
    $controllerClass = $matchedRoute['controller'];
    $controllerMethod = $matchedRoute['method'];
    
    $controller = new $controllerClass();
    $result = $controller->$controllerMethod();
    
    // Send response
    ResponseHandler::sendSuccess($result);
} catch (Exception $e) {
    ResponseHandler::sendError($e->getMessage(), $e->getCode() ?: 500);
}

// Handle different API endpoints
switch($path) {
    case 'user/profile':
        require_once __DIR__ . '/user/profile.php';
        break;

    case 'ai/sentiment':
        require_once __DIR__ . '/ai/sentiment.php';
        break;

    case 'recent-items':
        echo json_encode([
            'items' => [
                ['id' => 1, 'name' => 'Recent Item 1', 'type' => 'document', 'url' => '/dashboard', 'timestamp' => time()],
                ['id' => 2, 'name' => 'Recent Item 2', 'type' => 'task', 'url' => '/tasks', 'timestamp' => time()]
            ]
        ]);
        break;

    case 'ai/hr-trends':
        echo json_encode([
            'trends' => [
                ['month' => 'Jan', 'value' => 100],
                ['month' => 'Feb', 'value' => 120],
                ['month' => 'Mar', 'value' => 150]
            ]
        ]);
        break;

    case 'gamification/leaderboard':
        echo json_encode([
            'leaderboard' => [
                ['rank' => 1, 'name' => 'User 1', 'points' => 1000],
                ['rank' => 2, 'name' => 'User 2', 'points' => 900],
                ['rank' => 3, 'name' => 'User 3', 'points' => 800]
            ]
        ]);
        break;

    case 'gamification/progress':
        echo json_encode([
            'currentLevel' => 5,
            'points' => 750,
            'nextLevelPoints' => 1000,
            'achievements' => [
                ['id' => 1, 'name' => 'First Login', 'completed' => true],
                ['id' => 2, 'name' => 'Task Master', 'completed' => false]
            ]
        ]);
        break;

    default:
        http_response_code(404);
        echo json_encode([
            'error' => 'Endpoint not found',
            'path' => $path,
            'request_uri' => $request_uri,
            'base_path' => $base_path
        ]);
        break;
}
?> 