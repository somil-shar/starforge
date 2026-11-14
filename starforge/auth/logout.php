<?php
/**
 * StarForge - User Logout Handler
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';

logout_user();

session_start();
$_SESSION['flash_success'] = 'You have signed out successfully.';
header('Location: ' . BASE_URL . '/');
exit;
