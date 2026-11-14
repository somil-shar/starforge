<?php
/**
 * StarForge - Utility & Helper Functions
 */

require_once __DIR__ . '/../config/config.php';

/**
 * Sanitize and escape output for HTML
 */
function e(?string $value): string {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

/**
 * Generate or get existing CSRF token
 */
function get_csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate submitted CSRF token
 */
function validate_csrf_token(?string $token): bool {
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Strict SSRF-safe GitHub repository URL validator
 * Returns array with ['owner' => ..., 'repo' => ...] or false if invalid
 */
function validate_github_url(string $input): array|false {
    $input = trim($input);
    
    // Check if it's already an "owner/repo" format
    if (preg_match('/^[a-zA-Z0-9_\-\.]+[\/][a-zA-Z0-9_\-\.]+$/', $input)) {
        [$owner, $repo] = explode('/', $input);
        $repoClean = preg_replace('/\.git$/i', '', $repo);
        return [
            'owner' => $owner,
            'repo' => $repoClean,
            'clean_url' => "https://github.com/{$owner}/{$repoClean}"
        ];
    }

    // Check full URL
    $parsed = parse_url($input);
    if (!$parsed || empty($parsed['host']) || empty($parsed['path'])) {
        return false;
    }

    // Strictly enforce github.com domain (prevent internal SSRF such as 127.0.0.1, AWS metadata, etc.)
    $allowedHosts = ['github.com', 'www.github.com'];
    if (!in_array(strtolower($parsed['host']), $allowedHosts, true)) {
        return false;
    }

    $pathParts = array_values(array_filter(explode('/', trim($parsed['path'], '/'))));
    if (count($pathParts) < 2) {
        return false;
    }

    $owner = $pathParts[0];
    $repo = preg_replace('/\.git$/i', '', $pathParts[1]);

    // Reject path traversal tokens, dots, or invalid lengths
    if ($owner === '.' || $owner === '..' || $repo === '.' || $repo === '..') {
        return false;
    }
    if (strpos($owner, '..') !== false || strpos($repo, '..') !== false) {
        return false;
    }

    // Validate characters (GitHub username and repository name rules)
    if (!preg_match('/^[a-zA-Z0-9_\-\.]+$/', $owner) || !preg_match('/^[a-zA-Z0-9_\-\.]+$/', $repo)) {
        return false;
    }

    return [
        'owner' => $owner,
        'repo' => $repo,
        'clean_url' => "https://github.com/{$owner}/{$repo}"
    ];
}

/**
 * Format numbers compactly (e.g. 64200 -> 64.2k)
 */
function format_number(int $n): string {
    if ($n >= 1000000) {
        return round($n / 1000000, 1) . 'M';
    }
    if ($n >= 1000) {
        return round($n / 1000, 1) . 'k';
    }
    return (string)$n;
}

/**
 * Human friendly time difference
 */
function time_ago(string $datetime): string {
    $time = strtotime($datetime);
    if (!$time) return 'recently';
    $diff = time() - $time;
    if ($diff < 60) return 'just now';
    if ($diff < 3600) return floor($diff / 60) . 'm ago';
    if ($diff < 86400) return floor($diff / 3600) . 'h ago';
    if ($diff < 2592000) return floor($diff / 86400) . 'd ago';
    return date('M j, Y', $time);
}

/**
 * Return CSS color class and tier name based on quality score
 */
function get_score_tier(int $score): array {
    if ($score >= 90) {
        return ['class' => 'score-exceptional', 'badge' => 'Tier S • Outstanding', 'color' => '#10B981', 'glow' => 'rgba(16, 185, 129, 0.4)'];
    } elseif ($score >= 80) {
        return ['class' => 'score-excellent', 'badge' => 'Tier A • Production Ready', 'color' => '#22D3EE', 'glow' => 'rgba(34, 211, 238, 0.4)'];
    } elseif ($score >= 65) {
        return ['class' => 'score-good', 'badge' => 'Tier B • Solid Foundation', 'color' => '#8257FF', 'glow' => 'rgba(130, 87, 255, 0.4)'];
    } elseif ($score >= 50) {
        return ['class' => 'score-average', 'badge' => 'Tier C • Needs Polish', 'color' => '#F59E0B', 'glow' => 'rgba(245, 158, 11, 0.4)'];
    } else {
        return ['class' => 'score-poor', 'badge' => 'Tier D • Critical Gaps', 'color' => '#EF4444', 'glow' => 'rgba(239, 68, 68, 0.4)'];
    }
}

/**
 * Send JSON API response and terminate
 */
function json_response(array $data, int $statusCode = 200): void {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    exit;
}
