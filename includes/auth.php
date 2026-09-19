<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../db.php';

function get_logged_user() {
    global $pdo;
    if (!isset($_SESSION['user_id']) && isset($_SESSION['admin'])) {
        // Legacy session fallback
        $username = $_SESSION['admin'];
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_name'] = $user['name'];
            return $user;
        }
        // Fallback admin mock user array if legacy admin row in admins table
        return [
            'id' => 1,
            'name' => 'Administrator',
            'username' => $_SESSION['admin'],
            'email' => 'admin@gunvani.com',
            'role' => 'admin',
            'status' => 'active'
        ];
    }

    if (isset($_SESSION['user_id'])) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    return null;
}

function is_logged_in() {
    return isset($_SESSION['user_id']) || isset($_SESSION['admin']);
}

function is_admin() {
    if (!is_logged_in()) return false;
    $role = $_SESSION['user_role'] ?? 'admin';
    return strtolower($role) === 'admin';
}

function is_agent() {
    if (!is_logged_in()) return false;
    $role = $_SESSION['user_role'] ?? 'agent';
    return strtolower($role) === 'agent';
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit();
    }
}

function require_admin() {
    require_login();
    if (!is_admin()) {
        http_response_code(403);
        die("<!DOCTYPE html><html><head><title>403 Forbidden</title><link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' rel='stylesheet'></head><body class='bg-light text-center py-5'><div class='container'><h1 class='display-4 text-danger fw-bold'>403 Forbidden</h1><p class='lead text-muted'>Access Denied. You do not have permission to access this administrative resource.</p><a href='dashboard.php' class='btn btn-success fw-bold'>Back to Dashboard</a></div></body></html>");
    }
}
