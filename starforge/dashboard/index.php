<?php
/**
 * StarForge - Dashboard Forwarder
 * Automatically directs users to the primary Repository Analyzer tool.
 */
require_once __DIR__ . '/../config/config.php';
header('Location: ' . BASE_URL . '/dashboard/analyzer.php');
exit;
