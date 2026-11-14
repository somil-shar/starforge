/**
 * StarForge - Interactive Repository Analyzer Engine
 */

document.addEventListener('DOMContentLoaded', () => {
    const analyzerForm = document.getElementById('analyzer-form');
    if (analyzerForm) {
        analyzerForm.addEventListener('submit', handleAnalyzerSubmit);
    }
});

async function handleAnalyzerSubmit(e) {
    e.preventDefault();

    const input = document.getElementById('repo-url-input');
    const repoUrl = input ? input.value.trim() : '';

    if (!repoUrl) {
        showToast('Please enter a GitHub repository URL or owner/repo', 'error');
        return;
    }

    const progressBox = document.getElementById('analysis-progress');
    const resultBox = document.getElementById('analysis-result');
    const submitBtn = document.getElementById('analyzer-submit-btn');

    // UI State: Running
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span>Auditing...</span>';
    }
    if (progressBox) progressBox.style.display = 'block';
    if (resultBox) resultBox.style.display = 'none';

    // Step-by-step progress simulation for ultra-smooth SaaS feedback
    const steps = [
        "Resolving GitHub API endpoint...",
        "Inspecting repository metadata, stars & branches...",
        "Auditing README completeness & code samples...",
        "Checking open-source license & community health...",
        "Evaluating folder structure, CI/CD & test suites...",
        "Synthesizing 10-point score & actionable advice..."
    ];

    let stepIdx = 0;
    const progressText = document.getElementById('progress-step-text');
    const progressBar = document.getElementById('progress-fill');

    const interval = setInterval(() => {
        if (stepIdx < steps.length) {
            if (progressText) progressText.textContent = steps[stepIdx];
            if (progressBar) progressBar.style.width = `${((stepIdx + 1) / steps.length) * 90}%`;
            stepIdx++;
        }
    }, 450);

    try {
        const formData = new FormData();
        formData.append('repo_url', repoUrl);
        const csrfInput = document.querySelector('input[name="csrf_token"]');
        if (csrfInput) {
            formData.append('csrf_token', csrfInput.value);
        }

        const baseUrl = window.STARFORGE_API_BASE || window.STARFORGE_BASE_URL || (window.location.pathname.includes('/dashboard') ? window.location.pathname.split('/dashboard')[0] : '') || '';
        const endpoint = baseUrl + '/api/analyze.php';
        const res = await fetch(endpoint, {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json'
            }
        });

        clearInterval(interval);
        if (progressBar) progressBar.style.width = '100%';

        const text = await res.text();
        let data;
        try {
            data = JSON.parse(text);
        } catch (jsonErr) {
            if (!res.ok) {
                throw new Error(`Server returned error HTTP ${res.status} (${res.statusText || 'Not Found'})`);
            }
            throw new Error('Server returned an unexpected format. Please refresh the page.');
        }

        if (!data.success) {
            throw new Error(data.error || 'Failed to analyze repository');
        }

        setTimeout(() => {
            if (progressBox) progressBox.style.display = 'none';
            renderAnalysisResult(data);
            if (resultBox) {
                resultBox.style.display = 'block';
                resultBox.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<span>Analyze Repository</span>';
            }
        }, 500);

    } catch (err) {
        clearInterval(interval);
        if (progressBox) progressBox.style.display = 'none';
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<span>Analyze Repository</span>';
        }
        showToast(err.message, 'error');
    }
}

function renderAnalysisResult(payload) {
    const { repo, audit, report_id, is_fallback, fallback_note } = payload;
    const { overall_score, category_scores, strengths, weaknesses, recommendations, disclaimer } = audit;

    // Render header details
    const repoTitle = document.getElementById('res-repo-title');
    if (repoTitle) repoTitle.textContent = repo.full_name;

    const repoDesc = document.getElementById('res-repo-desc');
    if (repoDesc) repoDesc.textContent = repo.description || 'No description provided';

    const repoLink = document.getElementById('res-repo-link');
    if (repoLink) repoLink.href = repo.html_url;

    // Render Stats
    const starsEl = document.getElementById('res-stars');
    if (starsEl) starsEl.textContent = Number(repo.stargazers_count || 0).toLocaleString();

    const forksEl = document.getElementById('res-forks');
    if (forksEl) forksEl.textContent = Number(repo.forks_count || 0).toLocaleString();

    const licenseEl = document.getElementById('res-license');
    if (licenseEl) licenseEl.textContent = repo.license?.spdx_id || 'None';

    const langEl = document.getElementById('res-language');
    if (langEl) langEl.textContent = repo.language || 'Multi-language';

    // Fallback notice
    const fallbackBox = document.getElementById('res-fallback-notice');
    if (fallbackBox) {
        if (is_fallback) {
            fallbackBox.style.display = 'block';
            fallbackBox.textContent = fallback_note;
        } else {
            fallbackBox.style.display = 'none';
        }
    }

    // Overall Score Dial
    const scoreVal = document.getElementById('res-overall-score');
    if (scoreVal) scoreVal.textContent = overall_score;

    const scoreTier = document.getElementById('res-score-tier');
    if (scoreTier) {
        if (overall_score >= 90) scoreTier.textContent = 'Tier S • Outstanding';
        else if (overall_score >= 80) scoreTier.textContent = 'Tier A • Production Ready';
        else if (overall_score >= 65) scoreTier.textContent = 'Tier B • Solid Foundation';
        else if (overall_score >= 50) scoreTier.textContent = 'Tier C • Needs Polish';
        else scoreTier.textContent = 'Tier D • Critical Gaps';
    }

    // Category Bars
    const catContainer = document.getElementById('res-category-bars');
    if (catContainer && category_scores) {
        catContainer.innerHTML = '';
        const catLabels = {
            readme: 'README Quality & Completeness',
            description: 'Repository Description & SEO',
            topics: 'Topic Tagging & Discoverability',
            license: 'License & Open Source Compliance',
            structure: 'Folder Structure & Architecture',
            documentation: 'Docs Directory & Guides',
            activity: 'Recent Activity & Maintenance',
            code_organization: 'Tests & Dependency Manifests',
            community: 'Community Standards & Contributing',
            presentation: 'Visuals, Badges & Homepage'
        };

        for (const [key, score] of Object.entries(category_scores)) {
            const label = catLabels[key] || key;
            let barColor = '#10B981';
            if (score < 50) barColor = '#EF4444';
            else if (score < 75) barColor = '#8257FF';
            else if (score < 90) barColor = '#22D3EE';

            const item = document.createElement('div');
            item.className = 'cat-bar-item';
            item.innerHTML = `
                <div class="cat-bar-header">
                    <span style="color: var(--text-secondary); font-weight: 500;">${label}</span>
                    <span style="font-family: var(--font-mono); font-weight: 700; color: ${barColor}">${score}/100</span>
                </div>
                <div class="cat-bar-track">
                    <div class="cat-bar-fill" style="width: ${score}%; background: ${barColor};"></div>
                </div>
            `;
            catContainer.appendChild(item);
        }
    }

    // Strengths
    const strengthsList = document.getElementById('res-strengths-list');
    if (strengthsList) {
        strengthsList.innerHTML = strengths.map(s => `
            <li style="display: flex; gap: 0.75rem; margin-bottom: 0.65rem; color: #E2E8F0;">
                <span style="color: #10B981; font-weight: bold;">✓</span>
                <span>${s}</span>
            </li>
        `).join('') || '<li style="color: var(--text-muted);">No key strengths identified yet.</li>';
    }

    // Weaknesses
    const weaknessesList = document.getElementById('res-weaknesses-list');
    if (weaknessesList) {
        weaknessesList.innerHTML = weaknesses.map(w => `
            <li style="display: flex; gap: 0.75rem; margin-bottom: 0.65rem; color: #E2E8F0;">
                <span style="color: #EF4444; font-weight: bold;">⚠</span>
                <span>${w}</span>
            </li>
        `).join('') || '<li style="color: #10B981;">No major critical weaknesses detected!</li>';
    }

    // Recommendations
    const recsList = document.getElementById('res-recommendations-list');
    if (recsList) {
        recsList.innerHTML = recommendations.map(r => {
            const priorityClass = r.priority === 'High' ? 'tag-priority-high' : (r.priority === 'Medium' ? 'tag-priority-medium' : 'tag-priority-low');
            return `
                <div class="card" style="margin-bottom: 1rem; padding: 1.25rem; background: rgba(255,255,255,0.02);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                        <span class="tech-badge ${priorityClass}">${r.priority} Priority</span>
                        <span style="font-size: 0.8rem; color: var(--text-dim);">${r.category || 'Optimization'}</span>
                    </div>
                    <h4 style="font-size: 1.05rem; margin-bottom: 0.35rem;">${r.title}</h4>
                    <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 0.75rem;">${r.text}</p>
                    <button class="btn btn-secondary btn-sm" data-copy="${escapeHtml(r.text)}">Copy Recommendation</button>
                </div>
            `;
        }).join('');
    }

    // Pre-populate buttons
    const readmeActionBtn = document.getElementById('res-action-readme');
    if (readmeActionBtn) {
        const genUrl = `${window.STARFORGE_BASE_URL || ''}/dashboard/readme-generator.php?repo=${encodeURIComponent(repo.full_name)}`;
        readmeActionBtn.href = genUrl;
    }

    const seoActionBtn = document.getElementById('res-action-seo');
    if (seoActionBtn) {
        const seoUrl = `${window.STARFORGE_BASE_URL || ''}/dashboard/seo-assistant.php?repo=${encodeURIComponent(repo.full_name)}`;
        seoActionBtn.href = seoUrl;
    }
}

function escapeHtml(text) {
    return (text || '').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
}
