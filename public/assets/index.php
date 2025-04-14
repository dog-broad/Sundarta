<?php
// Disable session for static files
if (session_status() === PHP_SESSION_ACTIVE) {
    session_write_close();
}

// Asset handler
function getMimeType($filename) {
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $mimeTypes = [
        'js' => 'application/javascript',
        'mjs' => 'application/javascript',
        'css' => 'text/css',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf' => 'font/ttf',
        'eot' => 'application/vnd.ms-fontobject',
        'json' => 'application/json'
    ];
    
    return $mimeTypes[$ext] ?? 'application/octet-stream';
}

// Get the requested file path
$requestPath = $_SERVER['REQUEST_URI'];
$filePath = str_replace('/assets/', '', parse_url($requestPath, PHP_URL_PATH));

// Remove any '../' to prevent directory traversal
$filePath = str_replace('../', '', $filePath);

// Construct the full file path
$fullPath = __DIR__ . '/' . $filePath;

// Security check - prevent directory traversal
$realPath = realpath($fullPath);
if ($realPath === false || strpos($realPath, realpath(__DIR__)) !== 0) {
    header('HTTP/1.1 403 Forbidden');
    exit('Access denied');
}

// Check if file exists
if (!file_exists($fullPath)) {
    header('HTTP/1.1 404 Not Found');
    exit('File not found');
}

// Set proper MIME type
$mimeType = getMimeType($fullPath);
header('Content-Type: ' . $mimeType);

// Remove any PHP session headers
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

// Set caching headers
$etag = md5_file($fullPath);
header('ETag: "' . $etag . '"');
header('Cache-Control: public, max-age=31536000');
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');

// Check if-none-match header
if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH']) == '"' . $etag . '"') {
    header('HTTP/1.1 304 Not Modified');
    exit;
}

// Output file contents
readfile($fullPath);
exit; 