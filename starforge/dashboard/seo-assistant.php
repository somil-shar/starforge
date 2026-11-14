<?php
/**
 * StarForge - GitHub SEO & Discoverability Assistant
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

$repoParam = trim($_GET['repo'] ?? '');
$defaultName = '';
$defaultDesc = '';
$defaultLang = 'TypeScript';

if (!empty($repoParam)) {
    $parts = explode('/', $repoParam);
    $defaultName = end($parts);
}

$pageTitle = 'GitHub SEO Assistant';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container" style="padding-top: 3rem; padding-bottom: 5rem;">
    <!-- Page Header -->
    <div style="text-align: center; max-width: 720px; margin: 0 auto 3rem;">
        <div class="badge-pill">Search & Discovery</div>
        <h1 style="font-size: 2.5rem; margin-bottom: 1rem;">GitHub SEO Assistant</h1>
        <p style="color: var(--text-secondary); font-size: 1.05rem;">
            Help developers find your repository when searching on GitHub and Google. Get 15+ relevant topic tags and concise description ideas under 160 characters.
        </p>
    </div>

    <!-- Generator Input Card -->
    <div class="card" style="max-width: 800px; margin: 0 auto 3rem;">
        <form id="seo-form">
            <input type="hidden" name="csrf_token" value="<?= e(get_csrf_token()) ?>">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="project_name">Project Name *</label>
                    <input type="text" id="project_name" name="project_name" class="form-control" placeholder="e.g. express-starter" value="<?= e($defaultName) ?>" required>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="language">Primary Language</label>
                    <input type="text" id="language" name="language" class="form-control" placeholder="e.g. TypeScript, Python, Rust" value="<?= e($defaultLang) ?>">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="domain">Category / Focus</label>
                    <select id="domain" name="domain" class="form-control">
                        <option value="Developer Tools">Developer Tools & CLI</option>
                        <option value="Web Framework">Web Framework & Backend</option>
                        <option value="Frontend UI">Frontend & UI Components</option>
                        <option value="AI & Machine Learning">AI & Machine Learning</option>
                        <option value="DevOps & Cloud">DevOps & Infrastructure</option>
                        <option value="Security & Auth">Security & Authentication</option>
                        <option value="Database & Storage">Database & Caching</option>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="current_description">Current Tagline (Optional)</label>
                    <input type="text" id="current_description" name="current_description" class="form-control" placeholder="Your current one-liner" value="<?= e($defaultDesc) ?>">
                </div>
            </div>

            <button type="submit" id="generate-seo-btn" class="btn btn-primary" style="width: 100%; padding: 0.85rem;">
                Generate Topics & Descriptions
            </button>
        </form>
    </div>

    <!-- Output Container (Populated dynamically) -->
    <div id="seo-results-container" style="display: none; max-width: 900px; margin: 0 auto;">
        <!-- High Converting Descriptions -->
        <div class="card" style="margin-bottom: 2rem;">
            <h3 style="font-size: 1.25rem; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                <span style="color: var(--accent-cyan);">✦</span> Short Descriptions (Under 160 Characters)
            </h3>
            <p style="color: var(--text-muted); font-size: 0.875rem; margin-bottom: 1.25rem;">
                GitHub repo descriptions work best when concise. Pick whichever angle fits your project best:
            </p>
            <div id="seo-descriptions-list" style="display: flex; flex-direction: column; gap: 1rem;"></div>
        </div>

        <!-- Topic Tags Cloud -->
        <div class="card" style="margin-bottom: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <div>
                    <h3 style="font-size: 1.25rem; margin-bottom: 0.25rem;">Recommended GitHub Topics</h3>
                    <p style="color: var(--text-muted); font-size: 0.85rem;">Click any tag to copy, or copy all at once to paste into repo settings.</p>
                </div>
                <button type="button" id="copy-all-topics-btn" class="btn btn-secondary btn-sm">Copy All Topics</button>
            </div>
            <div id="seo-topics-cloud" style="display: flex; flex-wrap: wrap; gap: 0.65rem;"></div>
        </div>

        <!-- 2 Column: Project Naming & Documentation Headers -->
        <div class="grid-2">
            <!-- Project Naming Ideas -->
            <div class="card">
                <h4 style="font-size: 1.1rem; margin-bottom: 0.5rem;">Naming Ideas</h4>
                <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 1rem;">Common patterns used for clarity and package registry search.</p>
                <div id="seo-naming-list" style="display: flex; flex-direction: column; gap: 0.75rem;"></div>
            </div>

            <!-- Documentation Heading Blueprint -->
            <div class="card">
                <h4 style="font-size: 1.1rem; margin-bottom: 0.5rem;">Recommended Document Headings</h4>
                <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 1rem;">Standard sections that make your README easy to scan.</p>
                <div id="seo-headings-list" style="display: flex; flex-direction: column; gap: 0.75rem;"></div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('seo-form');
    if (form) {
        form.addEventListener('submit', handleSeoSubmit);
    }
});

async function handleSeoSubmit(e) {
    e.preventDefault();

    const btn = document.getElementById('generate-seo-btn');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span>Finding topics...</span>';
    }

    const formData = new FormData(e.target);

    try {
        const res = await fetch('<?= BASE_URL ?>/api/seo.php', {
            method: 'POST',
            body: formData,
            headers: { 'Accept': 'application/json' }
        });

        const data = await res.json();
        if (!data.success) {
            throw new Error(data.error || 'Failed to generate suggestions');
        }

        renderSeoResults(data);

        const container = document.getElementById('seo-results-container');
        if (container) {
            container.style.display = 'block';
            container.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
        showToast('Suggestions generated!', 'success');
    } catch (err) {
        showToast(err.message, 'error');
    } finally {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<span>Generate Topics & Descriptions</span>';
        }
    }
}

function renderSeoResults(data) {
    // 1. Descriptions
    const descContainer = document.getElementById('seo-descriptions-list');
    if (descContainer) {
        descContainer.innerHTML = data.descriptions.map(d => `
            <div style="background: rgba(255, 255, 255, 0.02); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 1rem 1.25rem; display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 260px;">
                    <div style="font-size: 0.75rem; text-transform: uppercase; font-weight: 700; color: var(--accent-cyan); margin-bottom: 0.35rem;">
                        ${d.type} <span style="color: var(--text-dim);">(${d.char_count} chars)</span>
                    </div>
                    <div style="font-size: 0.95rem; color: #fff;">${d.text}</div>
                </div>
                <button class="btn btn-secondary btn-sm" data-copy="${d.text.replace(/"/g, '&quot;')}">Copy</button>
            </div>
        `).join('');
    }

    // 2. Topics Cloud
    const topicsCloud = document.getElementById('seo-topics-cloud');
    if (topicsCloud) {
        topicsCloud.innerHTML = data.topics.map(t => `
            <span class="tech-badge" data-copy="${t}" style="cursor: pointer; padding: 0.35rem 0.75rem; font-size: 0.85rem; border-color: var(--border-bright);" title="Click to copy">
                # ${t}
            </span>
        `).join('');
    }

    const copyAllBtn = document.getElementById('copy-all-topics-btn');
    if (copyAllBtn) {
        copyAllBtn.onclick = () => {
            const allStr = data.topics.join(', ');
            navigator.clipboard.writeText(allStr).then(() => {
                showToast('All topics copied to clipboard!', 'success');
            });
        };
    }

    // 3. Naming suggestions
    const namingList = document.getElementById('seo-naming-list');
    if (namingList) {
        namingList.innerHTML = data.naming.map(n => `
            <div style="padding: 0.75rem; background: rgba(255, 255, 255, 0.02); border-radius: var(--radius-sm); border: 1px solid var(--border-subtle); display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div style="font-family: var(--font-mono); font-weight: 700; color: #C4B5FD;">${n.name}</div>
                    <div style="font-size: 0.75rem; color: var(--text-dim);">${n.rationale}</div>
                </div>
                <button class="btn btn-secondary btn-sm" data-copy="${n.name}">Copy</button>
            </div>
        `).join('');
    }

    // 4. Headings
    const headingsList = document.getElementById('seo-headings-list');
    if (headingsList) {
        headingsList.innerHTML = data.headings.map(h => `
            <div style="padding: 0.65rem 0.85rem; background: rgba(255, 255, 255, 0.02); border-radius: var(--radius-sm); border: 1px solid var(--border-subtle); display: flex; justify-content: space-between; align-items: center;">
                <span style="font-size: 0.9rem; color: #E2E8F0;">## ${h}</span>
                <button class="btn btn-secondary btn-sm" data-copy="## ${h.replace(/"/g, '&quot;')}">Copy</button>
            </div>
        `).join('');
    }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
