<?php
session_start([
    'cookie_lifetime' => 86400, // 1 day
    'cookie_secure'   => isset($_SERVER['HTTPS']),
    'cookie_httponly' => true,
    'cookie_samesite' => 'Strict',
    'use_strict_mode' => true
]);

function isLoggedIn() {
    return isset($_SESSION['user_id'], $_SESSION['logged_in']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        if (isApiRequest()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        } else {
            header('Location: /frontend/login.php');
            exit();
        }
    }

    // Session expiration (1 hour)
    if (time() - $_SESSION['logged_in'] > 3600) {
        logout();
        if (isApiRequest()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Session expired']);
            exit();
        } else {
            header('Location: /frontend/login.php?expired=1');
            exit();
        }
    }

    // Update last activity
    $_SESSION['logged_in'] = time();
}

function login($user_id, $username, $role) {
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user_id;
    $_SESSION['username'] = $username;
    $_SESSION['role'] = $role;
    $_SESSION['logged_in'] = time();
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function logout() {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}

function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

function getCurrentUserRole() {
    return $_SESSION['role'] ?? null;
}

function isAdmin() {
    return getCurrentUserRole() === 'admin';
}

function isSeller() {
    return getCurrentUserRole() === 'seller';
}

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        if (isApiRequest()) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Forbidden: Admin access required']);
            exit();
        } else {
            header('Location: /frontend/access-denied.php');
            exit();
        }
    }
}

function requireSeller() {
    requireLogin();
    if (!isSeller() && !isAdmin()) {
        if (isApiRequest()) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Forbidden: Seller access required']);
            exit();
        } else {
            header('Location: /frontend/access-denied.php');
            exit();
        }
    }
}

function validateCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function generateCsrfToken() {
    return $_SESSION['csrf_token'] ?? '';
}

function isApiRequest() {
    return (
        isset($_SERVER['HTTP_ACCEPT']) && 
        strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false
    ) || (
        isset($_SERVER['REQUEST_URI']) && 
        strpos($_SERVER['REQUEST_URI'], '/api/') !== false
    );
}
?>