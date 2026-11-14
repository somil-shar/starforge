<?php
/**
 * StarForge - Header Layout Component
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/functions.php';

$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? e($pageTitle) . ' • ' . APP_NAME : APP_NAME . ': Practical GitHub repo audits & clean docs' ?></title>
    <meta name="description" content="Check your GitHub repositories for missing documentation, clear setup steps, licenses, and discoverability. Built for developers.">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Design System -->
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/style.css?v=<?= APP_VERSION ?>">
    
    <!-- Base URL variables for JS -->
    <script>
        window.STARFORGE_BASE_URL = "<?= BASE_URL ?>";
        window.STARFORGE_API_BASE = "<?= BASE_URL ?>";
    </script>
</head>
<body>
    <div class="ambient-glow"></div>
    <div class="ambient-grid"></div>

    <div class="app-wrapper">
        <!-- Main Navigation Bar -->
        <header class="navbar">
            <div class="container nav-container">
                <a href="<?= BASE_URL ?>/" class="brand-logo">
                    <div class="brand-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                        </svg>
                    </div>
                    <span>STARFORGE</span>
                </a>

                <ul class="nav-links">
                    <li><a href="<?= BASE_URL ?>/dashboard/analyzer.php" class="nav-link <?= strpos($currentPath, 'analyzer') !== false ? 'active' : '' ?>">Repo Analyzer</a></li>
                    <li><a href="<?= BASE_URL ?>/dashboard/readme-generator.php" class="nav-link <?= strpos($currentPath, 'readme') !== false ? 'active' : '' ?>">README Generator</a></li>
                    <li><a href="<?= BASE_URL ?>/dashboard/seo-assistant.php" class="nav-link <?= strpos($currentPath, 'seo') !== false ? 'active' : '' ?>">GitHub SEO</a></li>
                </ul>

                <div class="nav-actions">
                    <a href="<?= BASE_URL ?>/dashboard/analyzer.php" class="btn btn-primary btn-sm">
                        + Audit a Repo
                    </a>
                    
                    <button class="btn btn-secondary btn-sm nav-mobile-toggle" style="display: none; padding: 0.4rem 0.6rem;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="3" y1="12" x2="21" y2="12"></line>
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <line x1="3" y1="18" x2="21" y2="18"></line>
                        </svg>
                    </button>
                </div>
            </div>
        </header>

        <!-- Flash messages -->
        <div class="container" style="margin-top: 1.5rem;">
            <?php if (!empty($_SESSION['flash_error'])): ?>
                <div class="alert alert-danger">
                    <?= e($_SESSION['flash_error']) ?>
                    <?php unset($_SESSION['flash_error']); ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($_SESSION['flash_success'])): ?>
                <div class="alert alert-success">
                    <?= e($_SESSION['flash_success']) ?>
                    <?php unset($_SESSION['flash_success']); ?>
                </div>
            <?php endif; ?>
        </div>
