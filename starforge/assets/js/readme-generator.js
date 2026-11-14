/**
 * StarForge - Interactive AI README Generator & Live Markdown Previewer
 */

document.addEventListener('DOMContentLoaded', () => {
    initMarkdownEditor();
    initReadmeGeneratorForm();
    initLicenseSearchPicker();
});

function initMarkdownEditor() {
    const rawEditor = document.getElementById('readme-raw-editor');
    const previewPane = document.getElementById('readme-preview-content');
    const copyBtn = document.getElementById('copy-readme-btn');
    const downloadBtn = document.getElementById('download-readme-btn');

    if (rawEditor && previewPane) {
        // Initial render
        renderMarkdown(rawEditor.value, previewPane);

        // Real-time render on input
        rawEditor.addEventListener('input', () => {
            renderMarkdown(rawEditor.value, previewPane);
        });
    }

    if (copyBtn && rawEditor) {
        copyBtn.addEventListener('click', () => {
            navigator.clipboard.writeText(rawEditor.value).then(() => {
                showToast('README.md markdown copied to clipboard!', 'success');
            }).catch(() => {
                showToast('Failed to copy', 'error');
            });
        });
    }

    if (downloadBtn && rawEditor) {
        downloadBtn.addEventListener('click', () => {
            const blob = new Blob([rawEditor.value], { type: 'text/markdown;charset=utf-8' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'README.md';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
            showToast('README.md file downloaded!', 'success');
        });
    }
}

function initReadmeGeneratorForm() {
    const form = document.getElementById('readme-wizard-form');
    if (!form) return;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const submitBtn = document.getElementById('generate-readme-btn');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span>Synthesizing README...</span>';
        }

        const formData = new FormData(form);

        try {
            const baseUrl = window.STARFORGE_API_BASE || window.STARFORGE_BASE_URL || (window.location.pathname.includes('/dashboard') ? window.location.pathname.split('/dashboard')[0] : '') || '';
            const endpoint = baseUrl + '/api/readme.php';
            const res = await fetch(endpoint, {
                method: 'POST',
                body: formData,
                headers: { 'Accept': 'application/json' }
            });

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
                throw new Error(data.error || 'Failed to generate README');
            }

            const rawEditor = document.getElementById('readme-raw-editor');
            const previewPane = document.getElementById('readme-preview-content');

            if (rawEditor) {
                rawEditor.value = data.markdown;
            }
            if (previewPane) {
                renderMarkdown(data.markdown, previewPane);
            }

            showToast('README generated successfully!', 'success');

            // Scroll to editor
            const editorCard = document.getElementById('readme-editor-card');
            if (editorCard) {
                editorCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }

        } catch (err) {
            showToast(err.message, 'error');
        } finally {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<span>Generate Professional README</span>';
            }
        }
    });
}

/**
 * Lightweight, safe client-side Markdown to HTML renderer
 */
function renderMarkdown(md, targetElement) {
    if (!md) {
        targetElement.innerHTML = '<p style="color: var(--text-dim); italic">Your rendered README preview will appear here...</p>';
        return;
    }

    let html = md
        // Escape basic HTML
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')

        // Code blocks
        .replace(/```([a-z0-9_-]*)\n([\s\S]*?)```/g, (match, lang, code) => {
            return `<pre><code class="language-${lang}">${code.trim()}</code></pre>`;
        })

        // Inline code
        .replace(/`([^`]+)`/g, '<code>$1</code>')

        // Badges / Images
        .replace(/!\[([^\]]*)\]\(([^)]+)\)/g, '<img src="$2" alt="$1" style="max-width: 100%; border-radius: 4px; display: inline-block; vertical-align: middle; margin: 2px;">')

        // Links
        .replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2" target="_blank" rel="noopener noreferrer">$1</a>')

        // Headers
        .replace(/^### (.*$)/gim, '<h3>$1</h3>')
        .replace(/^## (.*$)/gim, '<h2>$1</h2>')
        .replace(/^# (.*$)/gim, '<h1>$1</h1>')

        // Blockquotes
        .replace(/^\> (.*$)/gim, '<blockquote style="border-left: 3px solid var(--accent-cyan); padding-left: 1rem; color: var(--text-secondary); margin: 0.75rem 0;">$1</blockquote>')

        // Bold & Italic
        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
        .replace(/\*(.*?)\*/g, '<em>$1</em>')

        // Unordered lists
        .replace(/^\s*[\-\*]\s+(.*$)/gim, '<li style="margin-left: 1.5rem; margin-bottom: 0.25rem;">$1</li>')

        // Paragraphs & newlines
        .replace(/\n\n+/g, '</p><p style="margin-bottom: 0.75rem;">');

    targetElement.innerHTML = `<p style="margin-bottom: 0.75rem;">${html}</p>`;
}

/**
 * Searchable GitHub Licenses Picker & Real-Time Filter
 */
function initLicenseSearchPicker() {
    const searchInput = document.getElementById('license-search-input');
    const searchClear = document.getElementById('license-search-clear');
    const hiddenInput = document.getElementById('license');
    const noResults = document.getElementById('license-no-results');
    const chips = document.querySelectorAll('.lic-chip');
    const items = document.querySelectorAll('.license-item');

    if (!items.length) return;

    let activeFilter = 'all';

    // Filter items based on activeFilter and search query
    function filterLicenses() {
        const query = (searchInput ? searchInput.value : '').trim().toLowerCase();
        let visibleCount = 0;

        if (searchClear) {
            searchClear.style.display = query.length > 0 ? 'inline-block' : 'none';
        }

        items.forEach(item => {
            const itemCategory = item.dataset.category || '';
            const isPopular = item.dataset.popular === '1';
            const itemSearchText = item.dataset.search || '';

            // Category filter match
            let catMatch = false;
            if (activeFilter === 'all') catMatch = true;
            else if (activeFilter === 'popular') catMatch = isPopular;
            else if (activeFilter === itemCategory) catMatch = true;

            // Search query match
            const searchMatch = !query || itemSearchText.includes(query);

            if (catMatch && searchMatch) {
                item.style.display = 'block';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        if (noResults) {
            noResults.style.display = visibleCount === 0 ? 'block' : 'none';
        }
    }

    if (searchInput) {
        searchInput.addEventListener('input', filterLicenses);
    }

    if (searchClear) {
        searchClear.addEventListener('click', () => {
            if (searchInput) {
                searchInput.value = '';
                searchInput.focus();
                filterLicenses();
            }
        });
    }

    // Filter Chips
    chips.forEach(chip => {
        chip.addEventListener('click', () => {
            chips.forEach(c => c.classList.remove('active'));
            chip.classList.add('active');
            activeFilter = chip.dataset.filter || 'all';
            filterLicenses();
        });
    });

    // Select license item
    items.forEach(item => {
        item.addEventListener('click', () => {
            const id = item.dataset.id;
            const name = item.dataset.name;
            const cat = item.dataset.category;

            // Update hidden input submitted with form
            if (hiddenInput) {
                hiddenInput.value = id;
            }

            // Update selected box elements
            const badge = document.getElementById('selected-license-badge');
            const nameEl = document.getElementById('selected-license-name');
            const catEl = document.getElementById('selected-license-cat');

            if (badge) badge.textContent = id;
            if (nameEl) nameEl.textContent = name;
            if (catEl) catEl.textContent = `✓ ${cat.charAt(0).toUpperCase() + cat.slice(1)} • Selected for README`;

            // Active class styling
            items.forEach(i => i.classList.remove('selected'));
            item.classList.add('selected');

            // Show toast feedback
            if (typeof showToast === 'function') {
                showToast(`License set to ${id}`, 'info');
            }

            // Update editor markdown badge & license section
            updateEditorWithSelectedLicense(id, name);
        });
    });
}

function updateEditorWithSelectedLicense(licenseId, licenseName) {
    const rawEditor = document.getElementById('readme-raw-editor');
    const previewPane = document.getElementById('readme-preview-content');
    if (!rawEditor) return;

    let content = rawEditor.value;

    // Update license badge in markdown
    const badgeSafeId = encodeURIComponent(licenseId);
    content = content.replace(
        /img\.shields\.io\/badge\/license-[^-\)]+-([a-fA-F0-9]{6})\.svg/g,
        `img.shields.io/badge/license-${badgeSafeId}-$1.svg`
    );

    // Update license text section
    let licensePhrase = `Distributed under the ${licenseName}. See \`LICENSE\` for details.`;
    if (licenseId === 'Unlicense' || licenseId === 'CC0-1.0') {
        licensePhrase = `Dedicated to the public domain under ${licenseName}. See \`LICENSE\` for details.`;
    } else if (!licenseName.toLowerCase().includes('license')) {
        licensePhrase = `Distributed under the ${licenseName} License. See \`LICENSE\` for details.`;
    }

    content = content.replace(
        /## 📄 License\n\n[^\n]+/g,
        `## 📄 License\n\n${licensePhrase}`
    );

    rawEditor.value = content;
    if (previewPane) {
        renderMarkdown(content, previewPane);
    }
}
