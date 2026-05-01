<?php
// includes/auth.php
if (session_status() === PHP_SESSION_NONE) session_start();

function requireLogin(): void {
    if (empty($_SESSION['user_id'])) {
        header('Location: /login.php'); exit;
    }
}

function requireAdmin(): void {
    requireLogin();
    if ($_SESSION['user_role'] !== 'admin') {
        header('Location: /dashboard.php'); exit;
    }
}

function requireStudent(): void {
    requireLogin();
    if ($_SESSION['user_role'] !== 'student') {
        header('Location: /admin_manage.php'); exit;
    }
}

function generateCSRF(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRF(string $token): bool {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function isLoggedIn(): bool {
    return !empty($_SESSION['user_id']);
}
