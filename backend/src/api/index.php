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

// Get request path and method
$requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Remove /api prefix from path
$requestPath = str_replace('/api', '', $requestPath);

// Load routes
$routes = require_once __DIR__ . '/routes/routes.php';

// Find matching route
$matchedRoute = null;
foreach ($routes as $route) {
    if ($route['method'] === $requestMethod && $route['path'] === $requestPath) {
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
?> 