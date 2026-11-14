<?php
/**
 * StarForge - Repository Analyzer
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

$initialRepo = trim($_GET['repo'] ?? '');

$pageTitle = 'Repository Analyzer';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container" style="padding-top: 3rem; padding-bottom: 5rem;">
    <!-- Page Header -->
    <div style="text-align: center; max-width: 720px; margin: 0 auto 3rem;">
        <div class="badge-pill">10-Point Repository Inspection</div>
        <h1 style="font-size: 2.5rem; margin-bottom: 1rem;">Repository Analyzer</h1>
        <p style="color: var(--text-secondary); font-size: 1.05rem;">
            Check any public GitHub repo. See if it is missing a license, whether setup steps are clear, and what small changes will help more people use it.
        </p>
    </div>

    <!-- Search Input Form -->
    <div class="repo-search-box" style="margin-bottom: 2rem;">
        <form id="analyzer-form">
            <input type="hidden" name="csrf_token" value="<?= e(get_csrf_token()) ?>">
            <div class="search-input-group">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--accent-cyan)" stroke-width="2" style="margin-right: 0.5rem; flex-shrink: 0;">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" id="repo-url-input" placeholder="https://github.com/owner/repository or owner/repo" value="<?= e($initialRepo) ?>" required autofocus>
                <button type="submit" id="analyzer-submit-btn" class="btn btn-primary">
                    <span>Audit Repository</span>
                </button>
            </div>
        </form>

        <!-- Real Sample Chips -->
        <div class="hero-examples">
            <span>Try well-known projects:</span>
            <span class="example-tag" onclick="selectSampleRepo('expressjs/express')">expressjs/express</span>
            <span class="example-tag" onclick="selectSampleRepo('pallets/flask')">pallets/flask</span>
            <span class="example-tag" onclick="selectSampleRepo('facebook/react')">facebook/react</span>
            <span class="example-tag" onclick="selectSampleRepo('torvalds/linux')">torvalds/linux</span>
        </div>
    </div>

    <!-- Live Progress Bar -->
    <div id="analysis-progress" class="card" style="display: none; max-width: 800px; margin: 2rem auto; border-color: var(--accent-violet);">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
            <span id="progress-step-text" style="font-size: 0.95rem; font-weight: 600; color: #C4B5FD;">Connecting to GitHub API...</span>
            <span style="font-size: 0.8rem; color: var(--text-dim); font-family: var(--font-mono);">Inspecting files</span>
        </div>
        <div class="cat-bar-track" style="height: 6px;">
            <div id="progress-fill" class="cat-bar-fill" style="width: 15%; background: linear-gradient(90deg, var(--accent-violet), var(--accent-cyan));"></div>
        </div>
    </div>

    <!-- Analysis Results Container -->
    <div id="analysis-result" style="display: none; max-width: 1000px; margin: 3rem auto;">
        <?php include __DIR__ . '/_report_template.php'; ?>
    </div>
</div>

<script>
function selectSampleRepo(repo) {
    const input = document.getElementById('repo-url-input');
    if (input) {
        input.value = repo;
        document.getElementById('analyzer-form').dispatchEvent(new Event('submit'));
    }
}

// Auto-run if query param present
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('repo-url-input');
    if (input && input.value.trim().length > 0) {
        document.getElementById('analyzer-form').dispatchEvent(new Event('submit'));
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
