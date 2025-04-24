<?php
require_once __DIR__ . '/../vendor/autoload.php';
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

// JWT configuration
define('JWT_SECRET', getenv('JWT_SECRET') ?: 'your-secret-key');
define('JWT_ALGORITHM', 'HS256');
define('JWT_EXPIRATION', 3600); // 1 hour
define('JWT_REFRESH_EXPIRATION', 604800); // 7 days

/**
 * Generate a JWT token
 * @param array $payload Data to encode in the token
 * @param bool $isRefresh Whether this is a refresh token
 * @return string The JWT token
 */
function generateJWT($payload, $isRefresh = false) {
    $expiration = $isRefresh ? JWT_REFRESH_EXPIRATION : JWT_EXPIRATION;
    $payload['exp'] = time() + $expiration;
    $payload['iat'] = time();
    
    return JWT::encode($payload, JWT_SECRET, JWT_ALGORITHM);
}

/**
 * Verify and decode a JWT token
 * @param string $token The JWT token to verify
 * @param bool $isRefresh Whether this is a refresh token
 * @return object|false The decoded token payload or false if invalid
 */
function verifyJWT($token, $isRefresh = false) {
    try {
        $decoded = JWT::decode($token, new Key(JWT_SECRET, JWT_ALGORITHM));
        $payload = (array) $decoded;
        
        // Verify token type
        if ($isRefresh && !isset($payload['refresh'])) {
            throw new Exception('Invalid refresh token');
        }
        
        return $payload;
    } catch (Exception $e) {
        error_log("JWT verification error: " . $e->getMessage());
        return false;
    }
}

/**
 * Generate a new access token using refresh token
 * @param string $refreshToken The refresh token
 * @return array|false New access token and refresh token or false if invalid
 */
function refreshToken($refreshToken) {
    $payload = verifyJWT($refreshToken, true);
    if (!$payload) {
        return false;
    }
    
    // Remove refresh flag and generate new tokens
    unset($payload['refresh']);
    $newAccessToken = generateJWT($payload);
    $newRefreshToken = generateJWT(array_merge($payload, ['refresh' => true]), true);
    
    return [
        'access_token' => $newAccessToken,
        'refresh_token' => $newRefreshToken
    ];
}

/**
 * Get bearer token from Authorization header
 * @return string|null The token or null if not found
 */
function getBearerToken() {
    $headers = apache_request_headers();
    if (!isset($headers['Authorization'])) {
        return null;
    }

    if (preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
        return $matches[1];
    }

    return null;
}

/**
 * Check if request has valid JWT token
 * @return bool True if valid token present
 */
function hasValidToken() {
    $token = getBearerToken();
    if (!$token) {
        return false;
    }

    $decoded = verifyJWT($token);
    if (!$decoded) {
        return false;
    }

    // Check if token is expired
    if (isset($decoded['exp']) && $decoded['exp'] < time()) {
        return false;
    }

    return true;
}

/**
 * Get current user from JWT token
 * @return object|null User data from token or null if invalid
 */
function getCurrentUser() {
    $token = getBearerToken();
    if (!$token) {
        return null;
    }

    return verifyJWT($token);
} 