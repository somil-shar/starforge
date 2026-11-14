<?php
/**
 * StarForge - Session & Guest Mode Management
 * Stateless architecture with no database requirement.
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/functions.php';

/**
 * Check if current user is logged in
 */
function is_logged_in(): bool {
    return !empty($_SESSION['user_id']);
}

/**
 * Get currently authenticated user data
 */
function get_current_user_data(): ?array {
    return null;
}

/**
 * Force login requirement on protected pages (Public tools require no login)
 */
function require_login(): void {
    // Open access for all tools
}

/**
 * Set user login session
 */
function login_user(array $user): void {
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'] ?? 1;
    $_SESSION['username'] = $user['username'] ?? 'developer';
    $_SESSION['logged_in_time'] = time();
}

/**
 * Destroy session and logout
 */
function logout_user(): void {
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

/**
 * Log user activity (stateless no-op)
 */
function log_activity(int $userId, string $type, string $description, ?string $entityType = null, ?int $entityId = null): void {
    // Stateless mode: no database logging needed
}
