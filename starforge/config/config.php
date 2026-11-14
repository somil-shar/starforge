<?php
/**
 * StarForge - Configuration Settings
 * AI-Powered GitHub Repository Growth & Optimization Platform
 */

// Start session securely if not already started
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_samesite', 'Lax');
    session_start();
}

// Application Constants
define('APP_NAME', 'StarForge');
define('APP_TAGLINE', 'Build Better. Showcase Smarter. Earn Genuine Stars.');
define('APP_VERSION', '1.0.0');
define('APP_ENV', 'development'); // 'production' or 'development'

// Determine Base URL dynamically
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || ($_SERVER['SERVER_PORT'] ?? 80) == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));

// Normalize base path
if (strpos($scriptDir, '/starforge') !== false) {
    $basePath = substr($scriptDir, 0, strpos($scriptDir, '/starforge') + strlen('/starforge'));
} elseif (file_exists(__DIR__ . '/../starforge')) {
    $basePath = '/starforge';
} else {
    $basePath = '';
}

define('BASE_URL', rtrim($protocol . $host . $basePath, '/'));
define('ASSETS_URL', BASE_URL . '/assets');

// StarForge Zero-Database Architecture (Stateless, Fast, No MySQL/phpMyAdmin setup needed)
define('DB_ENABLED', false);

// Optional GitHub Personal Access Token (PAT) to raise rate limit from 60 to 5000 requests/hr
// Can be set via environment variable GITHUB_TOKEN or defined here
define('GITHUB_API_TOKEN', getenv('GITHUB_TOKEN') ?: '');

// Optional AI API Key (e.g. OpenAI / Gemini) for enhanced semantic suggestions
define('AI_API_KEY', getenv('AI_API_KEY') ?: '');

// Error reporting based on environment
if (APP_ENV === 'development') {
    ini_set('display_errors', '1');
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}
