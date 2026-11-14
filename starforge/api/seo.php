<?php
/**
 * StarForge - GitHub SEO Assistant API Endpoint
 * POST /api/seo.php (No Auth Required)
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'error' => 'Method not allowed. Use POST.'], 405);
}

$action = $_POST['action'] ?? 'generate';

if ($action === 'save') {
    json_response(['success' => true, 'message' => 'Suggestion saved to clipboard!']);
}

// Generate SEO suggestions
$projectName = trim($_POST['project_name'] ?? '');
$currentDesc = trim($_POST['current_description'] ?? '');
$language = trim($_POST['language'] ?? 'TypeScript');
$domain = trim($_POST['domain'] ?? 'Developer Tools');

if (empty($projectName)) {
    json_response(['success' => false, 'error' => 'Please enter a project or repository name.'], 422);
}

// Clean project name words
$cleanName = strtolower(preg_replace('/[^a-zA-Z0-9]/', ' ', $projectName));
$words = array_filter(explode(' ', $cleanName));

// 1. Generate 3 short descriptions (under 160 chars for GitHub & Google search snippets)
$descriptions = [
    [
        'type' => 'Direct Summary (Recommended)',
        'text' => "A lightweight {$domain} built with {$language} for straightforward developer workflows.",
        'char_count' => strlen("A lightweight {$domain} built with {$language} for straightforward developer workflows.")
    ],
    [
        'type' => 'Search-Focused',
        'text' => "Simple, type-safe {$language} {$domain} with zero dependencies and clear setup instructions.",
        'char_count' => strlen("Simple, type-safe {$language} {$domain} with zero dependencies and clear setup instructions.")
    ],
    [
        'type' => 'Action-Oriented',
        'text' => "Build and ship faster with {$projectName}: open source, well-tested, and easy to configure.",
        'char_count' => strlen("Build and ship faster with {$projectName}: open source, well-tested, and easy to configure.")
    ]
];

// 2. Curate 16-18 high-traffic GitHub topics
$langSlug = strtolower(preg_replace('/[^a-z0-9]/', '', $language));
$domainSlug = strtolower(preg_replace('/[^a-z0-9]/', '-', $domain));

$topicPool = [
    $langSlug,
    $domainSlug,
    'developer-tools',
    'open-source',
    'productivity',
    'cli',
    'api',
    'automation',
    'library',
    'dx',
    'clean-code',
    'zero-dependency',
    'typescript',
    'javascript',
    'utility'
];

foreach ($words as $w) {
    if (strlen($w) > 3 && !in_array($w, $topicPool)) {
        $topicPool[] = $w;
    }
}
$topics = array_slice(array_unique($topicPool), 0, 16);

// 3. Project Naming Ideas
$namingSuggestions = [
    [
        'name' => strtolower($words[0] ?? 'tool') . '-kit',
        'rationale' => 'Clear suffix indicating a focused toolkit.'
    ],
    [
        'name' => 'quick-' . strtolower($words[0] ?? 'flow'),
        'rationale' => 'Friendly, direct prefix communicating ease of use.'
    ],
    [
        'name' => strtolower($words[0] ?? 'core') . '.js',
        'rationale' => 'Standard registry format familiar to npm and package search.'
    ]
];

// 4. Document Heading Blueprint
$headingSuggestions = [
    '⚡ Quickstart (Under 60 Seconds)',
    '📦 Installation',
    '💡 Usage Examples',
    '📖 API Reference',
    '🤝 Contributing Guidelines'
];

json_response([
    'success' => true,
    'descriptions' => $descriptions,
    'topics' => $topics,
    'naming' => $namingSuggestions,
    'headings' => $headingSuggestions
]);
