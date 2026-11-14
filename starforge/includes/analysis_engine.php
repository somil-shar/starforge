<?php
/**
 * StarForge - Repository Analysis & Scoring Engine
 * Evaluates repositories across 10 critical dimensions to produce
 * actionable, non-hallucinated developer growth insights.
 */

class AnalysisEngine {

    /**
     * Run complete 10-point audit
     */
    public static function evaluate(array $auditData): array {
        $repo = $auditData['repo'] ?? [];
        $readme = $auditData['readme'] ?? ['exists' => false, 'content' => '', 'size' => 0];
        $files = $auditData['files'] ?? [];
        $commits = $auditData['commits'] ?? [];
        $community = $auditData['community'] ?? [];

        $fileNames = array_map(fn($f) => strtolower($f['name'] ?? ''), $files);

        $scores = [];
        $strengths = [];
        $weaknesses = [];
        $recommendations = [];

        // 1. README Quality & Availability (Weight: 15%)
        $readmeEval = self::evalReadme($readme);
        $scores['readme'] = $readmeEval['score'];
        $strengths = array_merge($strengths, $readmeEval['strengths']);
        $weaknesses = array_merge($weaknesses, $readmeEval['weaknesses']);
        $recommendations = array_merge($recommendations, $readmeEval['recommendations']);

        // 2. Repository Description & Tagline (Weight: 10%)
        $desc = trim($repo['description'] ?? '');
        $descScore = 0;
        if (empty($desc)) {
            $descScore = 20;
            $weaknesses[] = "Repository has no description. Projects without descriptions are frequently bypassed in search.";
            $recommendations[] = [
                'priority' => 'High',
                'category' => 'SEO & Discoverability',
                'title' => 'Add a Concise, Action-Oriented Description',
                'text' => 'Write a 1-sentence value proposition (60-150 chars) clearly describing what problem your project solves.'
            ];
        } elseif (strlen($desc) < 30) {
            $descScore = 60;
            $weaknesses[] = "Repository description is very brief ('{$desc}').";
            $recommendations[] = [
                'priority' => 'Medium',
                'category' => 'SEO & Discoverability',
                'title' => 'Expand Description with Core Keywords',
                'text' => 'Include key framework or problem domain keywords to improve internal GitHub search ranking.'
            ];
        } else {
            $descScore = 95;
            $strengths[] = "Clear and descriptive repository tagline present.";
        }
        $scores['description'] = $descScore;

        // 3. Repository Topics (Weight: 10%)
        $topics = $repo['topics'] ?? [];
        $topicCount = count($topics);
        if ($topicCount === 0) {
            $scores['topics'] = 25;
            $weaknesses[] = "Zero repository topics defined. Topics are critical for GitHub Explore and topic-tag search visibility.";
            $recommendations[] = [
                'priority' => 'High',
                'category' => 'SEO & Discoverability',
                'title' => 'Tag 5–10 Relevant GitHub Topics',
                'text' => 'Add tags for your programming language, framework, domain (e.g. cli, developer-tools, api), and use cases.'
            ];
        } elseif ($topicCount < 4) {
            $scores['topics'] = 70;
            $weaknesses[] = "Only {$topicCount} topics added. Recommended best practice is 5 to 10 topics.";
            $recommendations[] = [
                'priority' => 'Medium',
                'category' => 'SEO & Discoverability',
                'title' => 'Enrich Topic Tags',
                'text' => 'Add more granular tags representing your tech stack and architectural style.'
            ];
        } else {
            $scores['topics'] = 100;
            $strengths[] = "Healthy topic tagging ({$topicCount} topics) boosts ecosystem discoverability.";
        }

        // 4. License Availability & Open Source Compliance (Weight: 10%)
        $license = $repo['license'] ?? null;
        $hasLicenseFile = in_array('license', $fileNames) || in_array('license.md', $fileNames) || in_array('license.txt', $fileNames);
        if ($license && !empty($license['spdx_id']) && $license['spdx_id'] !== 'NOASSERTION') {
            $scores['license'] = 100;
            $strengths[] = "Recognized open-source license detected: " . $license['spdx_id'];
        } elseif ($hasLicenseFile) {
            $scores['license'] = 80;
            $strengths[] = "License file present in root directory.";
        } else {
            $scores['license'] = 15;
            $weaknesses[] = "No open-source license detected. Enterprise developers and contributors cannot legally use unlicensed code.";
            $recommendations[] = [
                'priority' => 'High',
                'category' => 'Governance',
                'title' => 'Attach an OSI-Approved License',
                'text' => 'Add an MIT, Apache 2.0, or BSD license file to unlock safe enterprise adoption and contribution.'
            ];
        }

        // 5. Folder Structure & Layout (Weight: 10%)
        $hasSrc = in_array('src', $fileNames) || in_array('lib', $fileNames) || in_array('app', $fileNames) || in_array('packages', $fileNames);
        $hasRootClean = count($files) <= 15;
        $structScore = 50;
        if ($hasSrc) {
            $structScore += 35;
            $strengths[] = "Standard source code separation detected (e.g. src/ or lib/).";
        } else {
            $weaknesses[] = "No dedicated src/, lib/, or app/ directory found. Root directory may be cluttered.";
            $recommendations[] = [
                'priority' => 'Low',
                'category' => 'Architecture',
                'title' => 'Organize Code into Subdirectories',
                'text' => 'Keep root reserved for configurations, manifests, and documentation, moving code to src/.'
            ];
        }
        if ($hasRootClean) {
            $structScore += 15;
        }
        $scores['structure'] = min(100, $structScore);

        // 6. Documentation Quality & Architecture (Weight: 10%)
        $hasDocsDir = in_array('docs', $fileNames) || in_array('documentation', $fileNames) || !empty($repo['has_wiki']);
        $hasChangelog = in_array('changelog.md', $fileNames) || in_array('history.md', $fileNames) || in_array('releases.md', $fileNames);
        $docScore = 40;
        if ($hasDocsDir) {
            $docScore += 35;
            $strengths[] = "Dedicated documentation directory or Wiki enabled.";
        } else {
            $recommendations[] = [
                'priority' => 'Medium',
                'category' => 'Documentation',
                'title' => 'Create a /docs Directory',
                'text' => 'Store architectural diagrams, API specs, and onboarding walkthroughs in a docs/ folder.'
            ];
        }
        if ($hasChangelog) {
            $docScore += 25;
            $strengths[] = "Changelog / Release history maintained.";
        }
        $scores['documentation'] = min(100, $docScore);

        // 7. Recent Activity & Maintenance Health (Weight: 10%)
        $commitCount = count($commits);
        $actScore = 60;
        if ($commitCount > 0) {
            $latestCommitDate = strtotime($commits[0]['date'] ?? '');
            $daysSinceLastCommit = $latestCommitDate ? floor((time() - $latestCommitDate) / 86400) : 100;
            if ($daysSinceLastCommit < 30) {
                $actScore = 95;
                $strengths[] = "Active repository: latest commit {$daysSinceLastCommit} days ago.";
            } elseif ($daysSinceLastCommit < 90) {
                $actScore = 75;
                $strengths[] = "Maintained repository (commit within last quarter).";
            } else {
                $actScore = 40;
                $weaknesses[] = "Stale activity: no public commits detected in the last {$daysSinceLastCommit} days.";
                $recommendations[] = [
                    'priority' => 'Medium',
                    'category' => 'Health',
                    'title' => 'Refresh Dependencies and Documentation',
                    'text' => 'Make a maintenance pass updating dependencies or adding automated tests to signal active stewardship.'
                ];
            }
        }
        $scores['activity'] = $actScore;

        // 8. Basic Code Organization & Tooling (Weight: 10%)
        $hasTests = in_array('tests', $fileNames) || in_array('test', $fileNames) || in_array('spec', $fileNames) || in_array('__tests__', $fileNames);
        $hasConfig = in_array('package.json', $fileNames) || in_array('composer.json', $fileNames) || in_array('cargo.toml', $fileNames) || in_array('pyproject.toml', $fileNames) || in_array('go.mod', $fileNames);
        $codeOrgScore = 40;
        if ($hasTests) {
            $codeOrgScore += 35;
            $strengths[] = "Automated test suite directory identified.";
        } else {
            $weaknesses[] = "No dedicated test/ directory found.";
            $recommendations[] = [
                'priority' => 'Medium',
                'category' => 'Quality',
                'title' => 'Include Automated Unit Tests',
                'text' => 'Repos with visible tests earn 3x higher trust from developers considering adopting your tool.'
            ];
        }
        if ($hasConfig) {
            $codeOrgScore += 25;
            $strengths[] = "Standard dependency package manifest present.";
        }
        $scores['code_organization'] = min(100, $codeOrgScore);

        // 9. Community Standards & Contribution Guidelines (Weight: 10%)
        $hasContributing = in_array('contributing.md', $fileNames) || !empty($community['files']['contributing']);
        $hasCoC = in_array('code_of_conduct.md', $fileNames) || !empty($community['files']['code_of_conduct']);
        $hasGithubWorkflows = in_array('.github', $fileNames);
        $commScore = 30;
        if ($hasContributing) {
            $commScore += 40;
            $strengths[] = "Clear CONTRIBUTING.md guidelines available for new contributors.";
        } else {
            $weaknesses[] = "Missing CONTRIBUTING.md. Prospective contributors have no clear instructions on how to submit PRs.";
            $recommendations[] = [
                'priority' => 'High',
                'category' => 'Community',
                'title' => 'Add CONTRIBUTING.md',
                'text' => 'Provide a simple 5-step guide for cloning, installing, testing, and opening PRs.'
            ];
        }
        if ($hasCoC) {
            $commScore += 20;
            $strengths[] = "Code of Conduct included.";
        }
        if ($hasGithubWorkflows) {
            $commScore += 10;
        }
        $scores['community'] = min(100, $commScore);

        // 10. Repository Presentation, Metadata & Visuals (Weight: 5%)
        $hasHomepage = !empty($repo['homepage']);
        $metaScore = 40;
        if ($hasHomepage) {
            $metaScore += 40;
            $strengths[] = "Live demo or official homepage configured in repository metadata: " . $repo['homepage'];
        } else {
            $weaknesses[] = "No official website/demo link defined in repository header.";
            $recommendations[] = [
                'priority' => 'Low',
                'category' => 'Presentation',
                'title' => 'Attach Demo or Documentation URL',
                'text' => 'Add your live preview or documentation website link directly into GitHub repo metadata.'
            ];
        }
        if ($hasGithubWorkflows) {
            $metaScore += 20;
            $strengths[] = "GitHub automation and CI/CD pipelines (.github/) detected.";
        }
        $scores['presentation'] = min(100, $metaScore);

        // Weighted Overall Score (0-100)
        $weights = [
            'readme' => 0.15,
            'description' => 0.10,
            'topics' => 0.10,
            'license' => 0.10,
            'structure' => 0.10,
            'documentation' => 0.10,
            'activity' => 0.10,
            'code_organization' => 0.10,
            'community' => 0.10,
            'presentation' => 0.05,
        ];

        $overallScore = 0;
        foreach ($weights as $key => $weight) {
            $overallScore += ($scores[$key] ?? 50) * $weight;
        }
        $overallScore = (int)round($overallScore);

        return [
            'overall_score' => $overallScore,
            'category_scores' => $scores,
            'strengths' => array_values(array_unique($strengths)),
            'weaknesses' => array_values(array_unique($weaknesses)),
            'recommendations' => $recommendations,
            'disclaimer' => "This score is an internal StarForge quality audit, not an official GitHub ranking or guarantee of stars. Its purpose is to guide repository enhancements that naturally attract genuine developer engagement."
        ];
    }

    /**
     * Deep evaluation of README content
     */
    private static function evalReadme(array $readme): array {
        if (empty($readme['exists']) || empty($readme['content'])) {
            return [
                'score' => 10,
                'strengths' => [],
                'weaknesses' => ["No README file found. A repository without a README almost never earns developer adoption or stars."],
                'recommendations' => [[
                    'priority' => 'High',
                    'category' => 'README & Onboarding',
                    'title' => 'Generate a Professional README',
                    'text' => 'Use the StarForge AI README Generator to scaffold a comprehensive overview, quickstart, and feature list.'
                ]]
            ];
        }

        $content = $readme['content'];
        $len = strlen($content);
        $score = 40;
        $strengths = [];
        $weaknesses = [];
        $recs = [];

        // Check badges
        if (preg_match('/\[!\[.*?\]\(.*?\)\]\(.*?\)|img\.shields\.io/i', $content)) {
            $score += 15;
            $strengths[] = "Status badges (build/license/version) displayed in README header.";
        } else {
            $recs[] = [
                'priority' => 'Medium',
                'category' => 'README & Onboarding',
                'title' => 'Add Shields.io Status Badges',
                'text' => 'Include CI status, license, npm/crate/packagist version, and test coverage badges.'
            ];
        }

        // Check Quickstart / Installation
        if (preg_match('/##?\s*(installation|getting\s*started|quick\s*start|setup)/i', $content)) {
            $score += 15;
            $strengths[] = "Clear Installation / Getting Started section present.";
        } else {
            $weaknesses[] = "No dedicated Installation or Getting Started header in README.";
            $recs[] = [
                'priority' => 'High',
                'category' => 'README & Onboarding',
                'title' => 'Add Copy-Paste Installation Instructions',
                'text' => 'Provide ready-to-run terminal commands for quick environment setup.'
            ];
        }

        // Check Usage / Code samples
        if (preg_match('/##?\s*(usage|how\s*to\s*use|examples|api)/i', $content) && preg_match('/```[a-z0-9_-]+/i', $content)) {
            $score += 15;
            $strengths[] = "Includes syntax-highlighted code usage snippets.";
        } else {
            $weaknesses[] = "Missing practical code usage examples.";
            $recs[] = [
                'priority' => 'High',
                'category' => 'README & Onboarding',
                'title' => 'Provide Minimal Working Code Example',
                'text' => 'Developers decide within 15 seconds whether to try a library based on readable usage snippets.'
            ];
        }

        // Check visual / image demo
        if (preg_match('/!\[.*?\]\(.*?\.(png|jpg|jpeg|gif|svg|webp)\)/i', $content) || stripos($content, '<img') !== false) {
            $score += 15;
            $strengths[] = "Rich visual preview (screenshots/GIFs) embedded in README.";
        } else {
            $recs[] = [
                'priority' => 'Low',
                'category' => 'Presentation',
                'title' => 'Embed Visual Demo Screenshot or GIF',
                'text' => 'Visual demos increase engagement by over 200% compared to text-only READMEs.'
            ];
        }

        if ($len < 400) {
            $weaknesses[] = "README is very brief ({$len} characters). Important details are missing.";
            $score = min($score, 50);
        }

        return [
            'score' => min(100, $score),
            'strengths' => $strengths,
            'weaknesses' => $weaknesses,
            'recommendations' => $recs
        ];
    }
}
