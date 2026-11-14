<?php
/**
 * StarForge - Repository Analysis API Endpoint
 * POST /api/analyze.php
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/github_service.php';
require_once __DIR__ . '/../includes/analysis_engine.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'error' => 'Method not allowed. Use POST.'], 405);
}

// Optional CSRF check if present
if (isset($_POST['csrf_token']) && !validate_csrf_token($_POST['csrf_token'])) {
    json_response(['success' => false, 'error' => 'Security token invalid or expired. Please refresh the page.'], 403);
}

$repoUrl = trim($_POST['repo_url'] ?? '');
if (empty($repoUrl)) {
    json_response(['success' => false, 'error' => 'Please provide a valid GitHub repository URL.'], 422);
}

// SSRF prevention: Strict validation of GitHub repository URL
$validated = validate_github_url($repoUrl);
if (!$validated) {
    json_response([
        'success' => false,
        'error' => 'Invalid repository format. Please provide a public GitHub URL (e.g. https://github.com/owner/repo or owner/repo).'
    ], 422);
}

$owner = $validated['owner'];
$repo = $validated['repo'];

try {
    // 1. Fetch complete data from GitHub Service
    $auditData = GitHubService::auditRepository($owner, $repo);

    if (!$auditData['success']) {
        json_response([
            'success' => false,
            'error' => $auditData['error'] ?? 'Could not retrieve repository data from GitHub.'
        ], 404);
    }

    // 2. Run the 10-point Analysis Engine
    $evaluation = AnalysisEngine::evaluate($auditData);

    $repoInfo = $auditData['repo'];
    $fullName = $repoInfo['full_name'] ?? "{$owner}/{$repo}";

    json_response([
        'success' => true,
        'report_id' => 1,
        'repository_id' => 1,
        'is_fallback' => $auditData['is_fallback'] ?? false,
        'fallback_note' => $auditData['fallback_note'] ?? '',
        'repo' => $repoInfo,
        'audit' => $evaluation
    ]);

} catch (Exception $e) {
    json_response([
        'success' => false,
        'error' => 'Internal server error while analyzing repository: ' . $e->getMessage()
    ], 500);
}
