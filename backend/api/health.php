<?php
/**
 * Health Check API
 * 
 * This endpoint is used by monitoring systems to check if the application is running correctly.
 * It checks database connection, disk space, and other system health indicators.
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/response.php';

// Check database connection
function checkDatabase() {
    global $conn;
    
    if (!$conn) {
        return [
            'status' => 'error',
            'message' => 'Database connection failed'
        ];
    }
    
    try {
        // Execute a simple query to verify the connection is working
        $result = $conn->query("SELECT 1");
        
        if ($result) {
            return [
                'status' => 'ok',
                'message' => 'Database connection successful'
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Database query failed'
            ];
        }
    } catch (Exception $e) {
        return [
            'status' => 'error',
            'message' => 'Database error: ' . $e->getMessage()
        ];
    }
}

// Check disk space
function checkDiskSpace() {
    $diskFree = disk_free_space('/');
    $diskTotal = disk_total_space('/');
    $diskUsed = $diskTotal - $diskFree;
    $percentUsed = ($diskUsed / $diskTotal) * 100;
    
    $status = 'ok';
    if ($percentUsed > 90) {
        $status = 'warning';
    } elseif ($percentUsed > 95) {
        $status = 'error';
    }
    
    return [
        'status' => $status,
        'percent_used' => round($percentUsed, 2),
        'free_space' => formatBytes($diskFree),
        'total_space' => formatBytes($diskTotal)
    ];
}

function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    
    $bytes /= pow(1024, $pow);
    
    return round($bytes, $precision) . ' ' . $units[$pow];
}

// Check application status
function checkApplication() {
    // Check if essential directories are writable
    $storageWritable = is_writable(__DIR__ . '/../../storage');
    $uploadsWritable = is_writable(__DIR__ . '/../../public/assets/uploads');
    
    $status = 'ok';
    $issues = [];
    
    if (!$storageWritable) {
        $status = 'error';
        $issues[] = 'Storage directory is not writable';
    }
    
    if (!$uploadsWritable) {
        $status = 'error';
        $issues[] = 'Uploads directory is not writable';
    }
    
    return [
        'status' => $status,
        'issues' => $issues
    ];
}

// Perform all health checks
$dbCheck = checkDatabase();
$diskCheck = checkDiskSpace();
$appCheck = checkApplication();

// Determine overall status
$overallStatus = 'ok';

if ($dbCheck['status'] === 'error' || $appCheck['status'] === 'error' || $diskCheck['status'] === 'error') {
    $overallStatus = 'error';
} elseif ($diskCheck['status'] === 'warning') {
    $overallStatus = 'warning';
}

// Prepare response
$response = [
    'status' => $overallStatus,
    'timestamp' => date('Y-m-d H:i:s'),
    'checks' => [
        'database' => $dbCheck,
        'disk' => $diskCheck,
        'application' => $appCheck
    ]
];

// Send response with appropriate HTTP status code
if ($overallStatus === 'ok') {
    sendJsonResponse($response, 200);
} elseif ($overallStatus === 'warning') {
    sendJsonResponse($response, 200); // Still return 200 for warnings
} else {
    sendJsonResponse($response, 500);
}

function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}