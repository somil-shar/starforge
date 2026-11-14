<?php
/**
 * StarForge - README Generator API Endpoint
 * POST /api/readme.php (No Auth Required)
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/licenses.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'error' => 'Method not allowed. Use POST.'], 405);
}

// Extract inputs
$projectName = trim($_POST['project_name'] ?? '');
$projectDesc = trim($_POST['project_description'] ?? '');
$languages = trim($_POST['programming_languages'] ?? 'TypeScript, JavaScript');
$featuresRaw = trim($_POST['features'] ?? '');
$installation = trim($_POST['installation'] ?? '');
$usage = trim($_POST['usage'] ?? '');
$screenshots = trim($_POST['screenshots'] ?? '');
$contributing = trim($_POST['contributing'] ?? '');
$license = trim($_POST['license'] ?? 'MIT');

if (empty($projectName)) {
    json_response(['success' => false, 'error' => 'Please provide a project name.'], 422);
}

// Convert features text into structured list
$featuresList = [];
if (!empty($featuresRaw)) {
    $lines = explode("\n", $featuresRaw);
    foreach ($lines as $line) {
        $cleaned = trim($line, " \t\n\r\0\x0B-*•");
        if (!empty($cleaned)) {
            $featuresList[] = $cleaned;
        }
    }
}
if (empty($featuresList)) {
    $featuresList = [
        "Lightweight architecture designed for straightforward developer workflows",
        "Clean, typed interfaces with zero unnecessary dependencies",
        "Comprehensive unit tests and clear error messages"
    ];
}

// Default installation if left blank
if (empty($installation)) {
    $slug = strtolower(preg_replace('/[^a-zA-Z0-9_-]/', '-', $projectName));
    $installation = "# Clone repository\ngit clone https://github.com/username/{$slug}.git\n\n# Navigate into project\ncd {$slug}\n\n# Install dependencies\nnpm install";
}

// Default usage if left blank
if (empty($usage)) {
    $usage = "// Quickstart\nimport { init } from '{$projectName}';\n\nconst app = init();\napp.start();";
}

// Format badges
$badgeSafeName = rawurlencode($projectName);
$badgeSafeLicense = rawurlencode($license);

$badgeSection = "<div align=\"center\">\n\n";
$badgeSection .= "  <h1>{$projectName}</h1>\n\n";
$badgeSection .= "  <p><strong>" . (!empty($projectDesc) ? $projectDesc : "A focused, well-maintained open source tool.") . "</strong></p>\n\n";
$badgeSection .= "  [![License](https://img.shields.io/badge/license-{$badgeSafeLicense}-8257FF.svg)](#license)\n";
$badgeSection .= "  [![Build Status](https://img.shields.io/badge/build-passing-10B981.svg)](#)\n";
$badgeSection .= "  [![PRs Welcome](https://img.shields.io/badge/PRs-welcome-22D3EE.svg)](#contributing)\n\n";
$badgeSection .= "</div>\n\n";

// Build Markdown
$md = $badgeSection;

$md .= "---\n\n";

if (!empty($screenshots)) {
    $md .= "## 📸 Preview\n\n";
    $md .= "![{$projectName} Demo]({$screenshots})\n\n";
}

$md .= "## 🚀 Key Features\n\n";
foreach ($featuresList as $feat) {
    $md .= "- " . $feat . "\n";
}
$md .= "\n";

$md .= "## 🛠️ Tech Stack\n\n";
$tags = array_map('trim', explode(',', $languages));
foreach ($tags as $t) {
    if (!empty($t)) {
        $md .= "`{$t}` ";
    }
}
$md .= "\n\n";

$md .= "## 📦 Installation\n\n";
$md .= "```bash\n{$installation}\n```\n\n";

$md .= "## 💡 Usage\n\n";
$md .= "```javascript\n{$usage}\n```\n\n";

$md .= "## 📁 Directory Structure\n\n";
$md .= "```\n";
$md .= "├── src/              # Source code\n";
$md .= "├── tests/            # Automated test suite\n";
$md .= "├── docs/             # Guides & documentation\n";
$md .= "└── README.md         # Project overview\n";
$md .= "```\n\n";

$md .= "## 🤝 Contributing\n\n";
if (!empty($contributing)) {
    $md .= "{$contributing}\n\n";
} else {
    $md .= "Contributions, issues, and feature requests are welcome!\n\n";
    $md .= "1. Fork the repository\n";
    $md .= "2. Create your feature branch (`git checkout -b feature/new-feature`)\n";
    $md .= "3. Commit your changes (`git commit -m 'Add new feature'`)\n";
    $md .= "4. Push to your branch (`git push origin feature/new-feature`)\n";
    $md .= "5. Open a Pull Request\n\n";
}

$licenseInfo = get_license_by_id($license);
$licenseName = $licenseInfo['name'] ?? ($license . ' License');
if ($license === 'Unlicense' || $license === 'CC0-1.0') {
    $licenseText = "Dedicated to the public domain under {$licenseName}. See `LICENSE` for details.";
} elseif (stripos($licenseName, 'license') !== false) {
    $licenseText = "Distributed under the {$licenseName}. See `LICENSE` for details.";
} else {
    $licenseText = "Distributed under the {$licenseName} License. See `LICENSE` for details.";
}

$md .= "## 📄 License\n\n";
$md .= "{$licenseText}\n";

json_response([
    'success' => true,
    'readme_id' => 1,
    'markdown' => $md
]);
