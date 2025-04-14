<?php
/**
 * Health Check API
 * 
 * This is a simplified health check endpoint for Docker health checks
 * and load balancers. It provides a basic check that the application
 * is responsive.
 */

// Skip any session handling to keep this endpoint lightweight
if (function_exists('session_status') && session_status() === PHP_SESSION_ACTIVE) {
    session_write_close();
}

// Check database connection
$dbStatus = 'unknown';

try {
    // Try to load config
    if (file_exists(__DIR__ . '/../../../.env')) {
        $lines = file(__DIR__ . '/../../../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '=') !== false) {
                list($name, $value) = explode('=', $line, 2);
                $_ENV[trim($name)] = trim($value);
            }
        }
    }
    
    // Get database connection parameters
    $dbHost = $_ENV['DB_HOST'] ?? 'db';
    $dbUser = $_ENV['DB_USER'] ?? 'root';
    $dbPass = $_ENV['DB_PASS'] ?? 'root';
    $dbName = $_ENV['DB_NAME'] ?? 'sundarta_db';
    
    // Create a simple database connection
    $mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
    
    if ($mysqli->connect_error) {
        $dbStatus = 'error';
    } else {
        // Execute a simple query
        $result = $mysqli->query("SELECT 1");
        if ($result) {
            $dbStatus = 'ok';
            $result->close();
        } else {
            $dbStatus = 'error';
        }
        $mysqli->close();
    }
} catch (Exception $e) {
    $dbStatus = 'error';
}

// Prepare response
$response = [
    'status' => $dbStatus === 'ok' ? 'ok' : 'error',
    'timestamp' => date('Y-m-d H:i:s'),
    'version' => '1.0.0',
    'db' => $dbStatus
];

// Send response with appropriate HTTP status code
http_response_code($dbStatus === 'ok' ? 200 : 500);
header('Content-Type: application/json');
echo json_encode($response);
exit; 