<?php
/**
 * StarForge - Landing Page
 * Honest, practical tools to help developers publish repos people can actually use.
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Make Your GitHub Projects Ready For Real Users';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section" id="top">
    <div class="container">
        <div class="badge-pill">
            <span style="color: var(--accent-cyan);">✦</span> Free repo audits and clean documentation tools
        </div>

        <h1 class="hero-title">
            Turn Your Code Into Projects Worth Starring.
        </h1>

        <p class="hero-subtitle">
            Most good open source projects get skipped simply because the README is missing a quickstart or there is no license. StarForge checks your repo for the essential details developers look for before trying your code.
        </p>

        <!-- Interactive Hero Audit Search Box -->
        <div class="repo-search-box">
            <form id="analyzer-form">
                <input type="hidden" name="csrf_token" value="<?= e(get_csrf_token()) ?>">
                <div class="search-input-group">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--accent-cyan)" stroke-width="2" style="margin-right: 0.5rem; flex-shrink: 0;">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" id="repo-url-input" placeholder="https://github.com/owner/repository or owner/repo" required>
                    <button type="submit" id="analyzer-submit-btn" class="btn btn-primary">
                        <span>Analyze My Repository</span>
                    </button>
                </div>
            </form>

            <div class="hero-examples">
                <span>Try real open source projects:</span>
                <span class="example-tag" onclick="runHeroAudit('expressjs/express')">expressjs/express</span>
                <span class="example-tag" onclick="runHeroAudit('pallets/flask')">pallets/flask</span>
                <span class="example-tag" onclick="runHeroAudit('facebook/react')">facebook/react</span>
                <span class="example-tag" onclick="runHeroAudit('torvalds/linux')">torvalds/linux</span>
            </div>
        </div>

        <!-- Live Progress Banner (Initially hidden) -->
        <div id="analysis-progress" class="card" style="display: none; max-width: 760px; margin: 2rem auto 0; border-color: var(--accent-violet);">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                <span id="progress-step-text" style="font-size: 0.95rem; font-weight: 600; color: #C4B5FD;">Connecting to GitHub API...</span>
                <span style="font-size: 0.8rem; color: var(--text-dim); font-family: var(--font-mono);">Running checks</span>
            </div>
            <div class="cat-bar-track" style="height: 6px;">
                <div id="progress-fill" class="cat-bar-fill" style="width: 15%; background: linear-gradient(90deg, var(--accent-violet), var(--accent-cyan));"></div>
            </div>
        </div>
    </div>
</section>

<!-- Inline Interactive Report Injection Target -->
<div class="container">
    <div id="analysis-result" style="display: none; max-width: 1000px; margin: 0 auto 5rem;">
        <?php include __DIR__ . '/dashboard/_report_template.php'; ?>
    </div>
</div>

<!-- What We Check (Visual Overview) -->
<section class="section" style="padding-top: 1rem;">
    <div class="container">
        <div class="card" style="border-color: rgba(130, 87, 255, 0.25); background: linear-gradient(180deg, rgba(14, 19, 34, 0.85) 0%, rgba(8, 11, 20, 0.95) 100%); padding: 2.5rem; position: relative; overflow: hidden;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; align-items: center;">
                <div>
                    <div class="tech-badge" style="color: var(--accent-cyan); border-color: var(--border-cyan); margin-bottom: 1rem;">
                        Practical Repo Diagnostics
                    </div>
                    <h2 style="font-size: 2.1rem; margin-bottom: 1rem; line-height: 1.3;">
                        What makes someone decide to star or skip a project?
                    </h2>
                    <p style="color: var(--text-secondary); font-size: 1rem; margin-bottom: 1.5rem; line-height: 1.7;">
                        When developers land on your repository, they usually look for three things in the first 15 seconds: what does this do, how do I install it, and can I legally use it? If any of those are unclear, they click back to Google.
                    </p>
                    <div style="display: flex; flex-direction: column; gap: 0.85rem; font-size: 0.925rem; color: #E2E8F0;">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <span style="color: #10B981; font-weight: bold;">✓</span>
                            <span>A quickstart with copy-pasteable terminal commands</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <span style="color: #10B981; font-weight: bold;">✓</span>
                            <span>A recognized open source license (MIT, Apache 2.0, or BSD)</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <span style="color: #10B981; font-weight: bold;">✓</span>
                            <span>Clear topic tags so people searching GitHub actually discover it</span>
                        </div>
                    </div>
                </div>

                <!-- Real Example Card (expressjs/express) -->
                <div style="background: rgba(8, 11, 20, 0.8); border: 1px solid var(--border-subtle); border-radius: var(--radius-lg); padding: 1.75rem; box-shadow: 0 20px 40px rgba(0,0,0,0.6);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--border-subtle); padding-bottom: 0.75rem;">
                        <span style="font-family: var(--font-mono); font-size: 0.9rem; font-weight: 700; color: #fff;">expressjs/express</span>
                        <span class="tech-badge" style="color: #10B981; background: rgba(16, 185, 129, 0.1);">Score: 91/100</span>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                        <div>
                            <div style="display: flex; justify-content: space-between; font-size: 0.8rem; margin-bottom: 0.25rem;">
                                <span style="color: var(--text-secondary);">README & Quickstart</span>
                                <span style="font-family: var(--font-mono); color: #10B981;">95%</span>
                            </div>
                            <div class="cat-bar-track"><div class="cat-bar-fill" style="width: 95%; background: #10B981;"></div></div>
                        </div>

                        <div>
                            <div style="display: flex; justify-content: space-between; font-size: 0.8rem; margin-bottom: 0.25rem;">
                                <span style="color: var(--text-secondary);">Community & Contributing Guidelines</span>
                                <span style="font-family: var(--font-mono); color: #22D3EE;">94%</span>
                            </div>
                            <div class="cat-bar-track"><div class="cat-bar-fill" style="width: 94%; background: #22D3EE;"></div></div>
                        </div>

                        <div>
                            <div style="display: flex; justify-content: space-between; font-size: 0.8rem; margin-bottom: 0.25rem;">
                                <span style="color: var(--text-secondary);">License Clarity</span>
                                <span style="font-family: var(--font-mono); color: #10B981;">100%</span>
                            </div>
                            <div class="cat-bar-track"><div class="cat-bar-fill" style="width: 100%; background: #10B981;"></div></div>
                        </div>

                        <div style="margin-top: 0.75rem; padding: 0.75rem 1rem; background: rgba(130, 87, 255, 0.08); border-radius: var(--radius-sm); border-left: 3px solid var(--accent-violet); font-size: 0.825rem; color: #C4B5FD;">
                            Tip: Adding a live sandbox link in your header lets people run your code before cloning.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Feature Highlights: 3 Core Pillars -->
<section class="section" id="features">
    <div class="container">
        <div class="section-header">
            <div class="badge-pill">Core Tools</div>
            <h2>Three Tools Built For Open Source Maintainers</h2>
            <p>Designed to save you time and help your work find its audience.</p>
        </div>

        <div class="grid-3">
            <!-- 1. Analyzer -->
            <div class="card card-interactive">
                <div class="feature-icon-box">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
                    </svg>
                </div>
                <h3 class="feature-title">Repository Analyzer</h3>
                <p class="feature-desc">
                    Checks 10 essential areas: README clarity, test folders, license files, commit activity, and contribution guidelines.
                </p>
                <a href="<?= BASE_URL ?>/dashboard/analyzer.php" style="margin-top: 1rem; display: inline-block; font-size: 0.85rem; font-weight: 600;">
                    Run an Audit &rarr;
                </a>
            </div>

            <!-- 2. README Studio -->
            <div class="card card-interactive">
                <div class="feature-icon-box" style="background: rgba(34, 211, 238, 0.12); color: var(--accent-cyan); border-color: rgba(34, 211, 238, 0.25);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                    </svg>
                </div>
                <h3 class="feature-title">README Generator</h3>
                <p class="feature-desc">
                    Creates clean, well-formatted Markdown with badges and code blocks. Never invents fake features: it uses only what you tell it.
                </p>
                <a href="<?= BASE_URL ?>/dashboard/readme-generator.php" style="margin-top: 1rem; display: inline-block; font-size: 0.85rem; font-weight: 600;">
                    Draft a README &rarr;
                </a>
            </div>

            <!-- 3. GitHub SEO -->
            <div class="card card-interactive">
                <div class="feature-icon-box" style="background: rgba(16, 185, 129, 0.12); color: #10B981; border-color: rgba(16, 185, 129, 0.25);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                    </svg>
                </div>
                <h3 class="feature-title">GitHub SEO Assistant</h3>
                <p class="feature-desc">
                    Gives you relevant topic tags and concise description ideas under 160 characters so your project appears in GitHub search.
                </p>
                <a href="<?= BASE_URL ?>/dashboard/seo-assistant.php" style="margin-top: 1rem; display: inline-block; font-size: 0.85rem; font-weight: 600;">
                    Find Topic Tags &rarr;
                </a>
            </div>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section class="section" id="how-it-works" style="background: rgba(14, 19, 34, 0.4); border-top: 1px solid var(--border-subtle); border-bottom: 1px solid var(--border-subtle);">
    <div class="container">
        <div class="section-header">
            <div class="badge-pill">The Process</div>
            <h2>How It Works</h2>
            <p>From initial inspection to a clean repository in four steps.</p>
        </div>

        <div class="grid-4">
            <div class="card step-card">
                <span class="step-number">01</span>
                <h3 style="font-size: 1.15rem; margin-bottom: 0.5rem; margin-top: 1.5rem;">Paste Repo URL</h3>
                <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.6;">
                    Enter any public GitHub repository. StarForge connects through the GitHub API without asking for write permissions.
                </p>
            </div>

            <div class="card step-card">
                <span class="step-number">02</span>
                <h3 style="font-size: 1.15rem; margin-bottom: 0.5rem; margin-top: 1.5rem;">See What's Missing</h3>
                <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.6;">
                    Get a clear breakdown of strengths and weaknesses: license issues, missing installation commands, or empty topic tags.
                </p>
            </div>

            <div class="card step-card">
                <span class="step-number">03</span>
                <h3 style="font-size: 1.15rem; margin-bottom: 0.5rem; margin-top: 1.5rem;">Apply Suggested Fixes</h3>
                <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.6;">
                    Copy suggested topics, paste recommended documentation headers, or generate a fresh README in the split editor.
                </p>
            </div>

            <div class="card step-card">
                <span class="step-number">04</span>
                <h3 style="font-size: 1.15rem; margin-bottom: 0.5rem; margin-top: 1.5rem;">Earn Real Stars</h3>
                <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.6;">
                    When prospective users can install your tool in under 60 seconds and know it is maintained, they are far more likely to star it.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Developer Benefits Section -->
<section class="section" id="benefits">
    <div class="container">
        <div class="section-header">
            <div class="badge-pill">Practical Help</div>
            <h2>Who This Is For</h2>
            <p>Whether you just finished your first side project or you maintain a library with thousands of downloads.</p>
        </div>

        <div class="grid-2">
            <div class="card" style="padding: 2.25rem;">
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem;">
                    <div class="feature-icon-box" style="margin-bottom: 0;">🌱</div>
                    <h3 style="font-size: 1.35rem;">Building Your First Public Projects</h3>
                </div>
                <ul style="list-style: none; display: flex; flex-direction: column; gap: 1rem; color: var(--text-secondary); font-size: 0.95rem;">
                    <li style="display: flex; gap: 0.75rem;">
                        <span style="color: var(--accent-cyan); font-weight: bold;">✦</span>
                        <span>Find out which standard files (LICENSE, CONTRIBUTING, .github) you forgot to include.</span>
                    </li>
                    <li style="display: flex; gap: 0.75rem;">
                        <span style="color: var(--accent-cyan); font-weight: bold;">✦</span>
                        <span>Write a readable README without fiddling with Markdown tables or broken badge URLs.</span>
                    </li>
                    <li style="display: flex; gap: 0.75rem;">
                        <span style="color: var(--accent-cyan); font-weight: bold;">✦</span>
                        <span>Present projects cleanly for job applications and portfolio reviews.</span>
                    </li>
                </ul>
            </div>

            <div class="card" style="padding: 2.25rem;">
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem;">
                    <div class="feature-icon-box" style="margin-bottom: 0; background: rgba(34, 211, 238, 0.12); color: var(--accent-cyan);">⚡</div>
                    <h3 style="font-size: 1.35rem;">Maintaining Established Libraries</h3>
                </div>
                <ul style="list-style: none; display: flex; flex-direction: column; gap: 1rem; color: var(--text-secondary); font-size: 0.95rem;">
                    <li style="display: flex; gap: 0.75rem;">
                        <span style="color: var(--accent-violet); font-weight: bold;">✦</span>
                        <span>Check that your topic tags match what developers actually type into GitHub search.</span>
                    </li>
                    <li style="display: flex; gap: 0.75rem;">
                        <span style="color: var(--accent-violet); font-weight: bold;">✦</span>
                        <span>Confirm your licensing and contribution policies meet the bar for commercial and enterprise teams.</span>
                    </li>
                    <li style="display: flex; gap: 0.75rem;">
                        <span style="color: var(--accent-violet); font-weight: bold;">✦</span>
                        <span>Quickly audit new repositories before announcing them on Reddit or Hacker News.</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Common Situations / Case Studies -->
<section class="section" style="background: rgba(14, 19, 34, 0.3);">
    <div class="container">
        <div class="section-header">
            <div class="badge-pill">Common Problems</div>
            <h2>Three Small Fixes That Change Everything</h2>
            <p>Real reasons good projects get overlooked, and how to fix them in minutes.</p>
        </div>

        <div class="grid-3">
            <div class="card">
                <div style="font-size: 0.85rem; font-weight: 700; color: #EF4444; margin-bottom: 0.5rem; text-transform: uppercase;">Problem 1: The Missing License</div>
                <h3 style="font-size: 1.15rem; margin-bottom: 0.75rem;">Great code, but companies cannot legally use it</h3>
                <p style="color: var(--text-secondary); font-size: 0.9rem; line-height: 1.6;">
                    If your repo does not have a LICENSE file, copyright law means all rights are reserved. Senior engineers and teams cannot install or contribute to unlicensed code. Adding an MIT or Apache file takes 30 seconds and immediately unlocks adoption.
                </p>
            </div>

            <div class="card">
                <div style="font-size: 0.85rem; font-weight: 700; color: #F59E0B; margin-bottom: 0.5rem; text-transform: uppercase;">Problem 2: Zero Topic Tags</div>
                <h3 style="font-size: 1.15rem; margin-bottom: 0.75rem;">Invisible to GitHub Explore and search queries</h3>
                <p style="color: var(--text-secondary); font-size: 0.9rem; line-height: 1.6;">
                    GitHub relies heavily on repository topics. If you build a Go CLI tool and add zero topics, you never show up when developers filter by "cli", "go", or "developer-tools". Adding 6 to 10 accurate topics brings steady organic traffic.
                </p>
            </div>

            <div class="card">
                <div style="font-size: 0.85rem; font-weight: 700; color: #34D399; margin-bottom: 0.5rem; text-transform: uppercase;">Problem 3: The 30-Second Test</div>
                <h3 style="font-size: 1.15rem; margin-bottom: 0.75rem;">No quickstart snippet in the first screen</h3>
                <p style="color: var(--text-secondary); font-size: 0.9rem; line-height: 1.6;">
                    If someone has to scroll through three paragraphs of design philosophy to find the install command, they usually leave. Putting a three-line code example above the fold dramatically increases adoption.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="section" id="faq">
    <div class="container" style="max-width: 800px;">
        <div class="section-header">
            <div class="badge-pill">Questions & Answers</div>
            <h2>Frequently Asked Questions</h2>
            <p>Clear details about how StarForge works.</p>
        </div>

        <div style="display: flex; flex-direction: column; gap: 1rem;">
            <div class="card faq-item active" style="padding: 1.25rem 1.5rem; cursor: pointer;">
                <div class="faq-question" style="display: flex; justify-content: space-between; align-items: center; font-weight: 600; font-size: 1.05rem;">
                    <span>Does StarForge buy, sell, or exchange GitHub stars?</span>
                    <span style="color: var(--accent-cyan); font-weight: bold;">+</span>
                </div>
                <div class="faq-answer" style="margin-top: 0.75rem; color: var(--text-secondary); font-size: 0.925rem; line-height: 1.6;">
                    <strong>No, never.</strong> StarForge does not use bots, star exchanges, or automated engagement. Real developers star projects that are well-documented, easy to install, and clearly maintained. We help you fix the practical issues holding your project back so people choose to star it on their own.
                </div>
            </div>

            <div class="card faq-item" style="padding: 1.25rem 1.5rem; cursor: pointer;">
                <div class="faq-question" style="display: flex; justify-content: space-between; align-items: center; font-weight: 600; font-size: 1.05rem;">
                    <span>Can I audit private repositories?</span>
                    <span style="color: var(--accent-cyan); font-weight: bold;">+</span>
                </div>
                <div class="faq-answer" style="margin-top: 0.75rem; color: var(--text-secondary); font-size: 0.925rem; line-height: 1.6;">
                    Currently, StarForge only audits public repositories. This ensures zero risk of sensitive code exposure.
                </div>
            </div>

            <div class="card faq-item" style="padding: 1.25rem 1.5rem; cursor: pointer;">
                <div class="faq-question" style="display: flex; justify-content: space-between; align-items: center; font-weight: 600; font-size: 1.05rem;">
                    <span>What does the quality score actually measure?</span>
                    <span style="color: var(--accent-cyan); font-weight: bold;">+</span>
                </div>
                <div class="faq-answer" style="margin-top: 0.75rem; color: var(--text-secondary); font-size: 0.925rem; line-height: 1.6;">
                    It evaluates 10 practical dimensions: README quality, concise description, topic tagging, open-source license, clean directory structure, extra documentation, recent commits, automated tests, contribution instructions, and repository presentation. It is our internal audit score, not an official GitHub rating.
                </div>
            </div>

            <div class="card faq-item" style="padding: 1.25rem 1.5rem; cursor: pointer;">
                <div class="faq-question" style="display: flex; justify-content: space-between; align-items: center; font-weight: 600; font-size: 1.05rem;">
                    <span>Does the README generator make up features that do not exist?</span>
                    <span style="color: var(--accent-cyan); font-weight: bold;">+</span>
                </div>
                <div class="faq-answer" style="margin-top: 0.75rem; color: var(--text-secondary); font-size: 0.925rem; line-height: 1.6;">
                    No. The generator builds clean GitHub-Flavored Markdown using only the features, tech stack, and setup steps you supply. It never fabricates benchmarks or claims capabilities you did not specify.
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Final Call to Action -->
<section class="section" style="text-align: center; padding-bottom: 6rem;">
    <div class="container" style="max-width: 760px;">
        <div class="card" style="padding: 3.5rem 2.5rem; background: radial-gradient(circle at center, rgba(130, 87, 255, 0.15) 0%, rgba(14, 19, 34, 0.9) 70%); border-color: var(--border-bright);">
            <h2 style="font-size: 2.1rem; margin-bottom: 1rem;">Ready to check your repository?</h2>
            <p style="color: var(--text-secondary); font-size: 1.05rem; margin-bottom: 2rem;">
                Run a free audit on your project in 10 seconds.
            </p>
            <div style="display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap;">
                <a href="<?= BASE_URL ?>/dashboard/analyzer.php" class="btn btn-primary btn-lg">
                    Run Free Repo Audit
                </a>
                <a href="<?= BASE_URL ?>/dashboard/readme-generator.php" class="btn btn-secondary btn-lg">
                    Build a Clean README
                </a>
            </div>
        </div>
    </div>
</section>

<script>
function runHeroAudit(repo) {
    const input = document.getElementById('repo-url-input');
    if (input) {
        input.value = repo;
        document.getElementById('analyzer-form').dispatchEvent(new Event('submit'));
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
