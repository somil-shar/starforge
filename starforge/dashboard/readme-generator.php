<?php
/**
 * StarForge - README Generator & Live Markdown Studio
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/licenses.php';

$allLicenses = get_github_licenses();

$repoParam = trim($_GET['repo'] ?? '');
$defaultName = '';
$defaultDesc = '';
$defaultLang = 'TypeScript, React';

if (!empty($repoParam)) {
    $parts = explode('/', $repoParam);
    $defaultName = end($parts);
}

$pageTitle = 'README Generator';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container" style="padding-top: 3rem; padding-bottom: 5rem;">
    <!-- Page Header -->
    <div style="text-align: center; max-width: 720px; margin: 0 auto 3rem;">
        <div class="badge-pill">Markdown Studio</div>
        <h1 style="font-size: 2.5rem; margin-bottom: 1rem;">README Generator</h1>
        <p style="color: var(--text-secondary); font-size: 1.05rem;">
            Draft a clean, well-structured README with badges, install commands, and code samples. You can edit the markdown directly and see the live preview update instantly.
        </p>
    </div>

    <!-- 2 Column Layout: Wizard Form on Left, Live Editor & Preview on Right -->
    <div style="display: grid; grid-template-columns: 420px 1fr; gap: 2rem; align-items: start;">
        <!-- Left Column: Input Wizard -->
        <div class="card">
            <h3 style="font-size: 1.2rem; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
                <span style="color: var(--accent-cyan);">✦</span> Project Details
            </h3>

            <form id="readme-wizard-form">
                <input type="hidden" name="csrf_token" value="<?= e(get_csrf_token()) ?>">

                <div class="form-group">
                    <label class="form-label" for="project_name">Project Name *</label>
                    <input type="text" id="project_name" name="project_name" class="form-control" placeholder="e.g. quick-cache" value="<?= e($defaultName) ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="project_description">Short Summary *</label>
                    <input type="text" id="project_description" name="project_description" class="form-control" placeholder="A lightweight in-memory cache for Node.js apps" value="<?= e($defaultDesc) ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="programming_languages">Tech Stack / Languages (Comma-separated)</label>
                    <input type="text" id="programming_languages" name="programming_languages" class="form-control" placeholder="TypeScript, Node.js" value="<?= e($defaultLang) ?>">
                </div>

                <div class="form-group">
                    <label class="form-label" for="features">Key Features (One per line)</label>
                    <textarea id="features" name="features" class="form-control" style="min-height: 90px;" placeholder="Fast key-value storage with TTL support&#10;Zero external dependencies&#10;Full TypeScript typings included"></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label" for="installation">Install Command (Optional)</label>
                    <textarea id="installation" name="installation" class="form-control" style="min-height: 70px; font-family: var(--font-mono); font-size: 0.85rem;" placeholder="npm install quick-cache"></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label" for="usage">Quick Usage Example (Optional)</label>
                    <textarea id="usage" name="usage" class="form-control" style="min-height: 70px; font-family: var(--font-mono); font-size: 0.85rem;" placeholder="import { createCache } from 'quick-cache';&#10;&#10;const cache = createCache({ ttlSeconds: 60 });&#10;cache.set('user_1', { name: 'Alex' });"></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label" for="screenshots">Screenshot or Demo GIF URL (Optional)</label>
                    <input type="url" id="screenshots" name="screenshots" class="form-control" placeholder="https://raw.githubusercontent.com/.../demo.png">
                </div>

                <div class="form-group">
                    <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 0.5rem;">
                        <label class="form-label" style="margin-bottom: 0;">Open Source License *</label>
                        <span style="font-size: 0.75rem; color: var(--text-dim);"><?= count($allLicenses) ?> GitHub & SPDX Licenses</span>
                    </div>

                    <!-- Hidden input submitted with the form -->
                    <input type="hidden" id="license" name="license" value="MIT">

                    <!-- Active License Indicator -->
                    <div id="license-selected-box" style="display: flex; align-items: center; justify-content: space-between; background: rgba(130, 87, 255, 0.12); border: 1px solid rgba(130, 87, 255, 0.4); border-radius: var(--radius-md); padding: 0.75rem 1rem; margin-bottom: 0.65rem;">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <span class="license-badge-pill" id="selected-license-badge">MIT</span>
                            <div>
                                <div style="font-weight: 600; font-size: 0.95rem; color: #fff;" id="selected-license-name">MIT License</div>
                                <div style="font-size: 0.75rem; color: #34D399;" id="selected-license-cat">✓ Permissive • Selected for README</div>
                            </div>
                        </div>
                        <span class="tech-badge" style="background: rgba(16, 185, 129, 0.2); color: #34D399; font-size: 0.7rem; border-color: rgba(16, 185, 129, 0.3);">Active</span>
                    </div>

                    <!-- Searchable License Explorer (Directly visible for instant search) -->
                    <div class="license-picker-dropdown" style="margin-top: 0; box-shadow: none; border-color: rgba(255, 255, 255, 0.1);">
                        <div class="license-picker-header">
                            <div class="license-search-box">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--accent-cyan); flex-shrink: 0;">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                </svg>
                                <input type="text" id="license-search-input" placeholder="Search 39+ GitHub licenses (e.g. apache, gpl, bsd, mit, mozilla)..." autocomplete="off">
                                <button type="button" id="license-search-clear" style="display: none; background: transparent; border: none; color: var(--text-dim); cursor: pointer; font-size: 1.1rem; line-height: 1; padding: 0 0.25rem;">&times;</button>
                            </div>
                            
                            <!-- Category Filter Chips -->
                            <div class="license-filter-chips">
                                <button type="button" class="lic-chip active" data-filter="all">All (<?= count($allLicenses) ?>)</button>
                                <button type="button" class="lic-chip" data-filter="popular">Popular</button>
                                <button type="button" class="lic-chip" data-filter="permissive">Permissive</button>
                                <button type="button" class="lic-chip" data-filter="copyleft">Copyleft</button>
                                <button type="button" class="lic-chip" data-filter="weak-copyleft">Weak Copyleft</button>
                                <button type="button" class="lic-chip" data-filter="public-domain">Public Domain</button>
                            </div>
                        </div>

                        <!-- Licenses List -->
                        <div id="license-list-container" class="license-list-container" style="max-height: 220px;">
                            <?php foreach ($allLicenses as $lic): ?>
                                <div class="license-item <?= $lic['id'] === 'MIT' ? 'selected' : '' ?>" 
                                     data-id="<?= e($lic['id']) ?>"
                                     data-name="<?= e($lic['name']) ?>"
                                     data-category="<?= e($lic['category']) ?>"
                                     data-popular="<?= $lic['popular'] ? '1' : '0' ?>"
                                     data-summary="<?= e($lic['summary']) ?>"
                                     data-search="<?= strtolower(e($lic['id'] . ' ' . $lic['name'] . ' ' . $lic['category'] . ' ' . $lic['summary'] . ' ' . implode(' ', $lic['permissions'] ?? []))) ?>">
                                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.25rem; gap: 0.5rem;">
                                        <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                                            <span class="license-item-code"><?= e($lic['id']) ?></span>
                                            <span class="license-item-name"><?= e($lic['name']) ?></span>
                                        </div>
                                        <span class="license-item-tag tag-<?= e($lic['category']) ?>"><?= ucfirst(e($lic['category'])) ?></span>
                                    </div>
                                    <p class="license-item-desc"><?= e($lic['summary']) ?></p>
                                    <div class="license-item-perms">
                                        <?php foreach (array_slice($lic['permissions'] ?? [], 0, 3) as $perm): ?>
                                            <span>✓ <?= e($perm) ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <div id="license-no-results" style="display: none; padding: 1.5rem 1rem; text-align: center; color: var(--text-dim); font-size: 0.85rem;">
                                No licenses match your search. Try searching for "MIT", "Apache", "GPL", or "BSD".
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" id="generate-readme-btn" class="btn btn-primary" style="width: 100%; padding: 0.85rem;">
                    <span>Generate README Markdown</span>
                </button>
            </form>
        </div>

        <!-- Right Column: Split Studio (Raw Markdown + Rendered Preview) -->
        <div id="readme-editor-card" class="card" style="padding: 1.25rem;">
            <!-- Top Controls -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.75rem;">
                <div style="display: flex; gap: 0.5rem;">
                    <span class="tech-badge" style="background: rgba(130, 87, 255, 0.15); color: #C4B5FD;">Live Split Studio</span>
                    <span style="font-size: 0.8rem; color: var(--text-dim); display: flex; align-items: center;">Real-time preview</span>
                </div>
                <div style="display: flex; gap: 0.75rem;">
                    <button type="button" id="copy-readme-btn" class="btn btn-secondary btn-sm">
                        📋 Copy Markdown
                    </button>
                    <button type="button" id="download-readme-btn" class="btn btn-cyan btn-sm">
                        ⬇ Download README.md
                    </button>
                </div>
            </div>

            <!-- Split Container -->
            <div class="editor-split-container">
                <!-- Raw Editor Pane -->
                <div class="editor-pane">
                    <div class="pane-toolbar">
                        <span style="font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); text-transform: uppercase;">Markdown Source</span>
                        <span style="font-size: 0.75rem; color: var(--text-dim); font-family: var(--font-mono);">README.md</span>
                    </div>
                    <textarea id="readme-raw-editor" style="width: 100%; height: 100%; border: none; background: transparent; color: #E2E8F0; padding: 1rem; font-family: var(--font-mono); font-size: 0.85rem; line-height: 1.6; resize: none; outline: none;"><div align="center">

  <h1><?= e($defaultName ?: 'project-name') ?></h1>

  <p><strong><?= e($defaultDesc ?: 'A clear, focused open source utility.') ?></strong></p>

  [![License](https://img.shields.io/badge/license-MIT-8257FF.svg)](#license)
  [![Build Status](https://img.shields.io/badge/build-passing-10B981.svg)](#)
  [![PRs Welcome](https://img.shields.io/badge/PRs-welcome-22D3EE.svg)](#contributing)

</div>

---

## 🚀 Key Features

- **Fast & Simple**: Minimal overhead with an intuitive API.
- **TypeScript Ready**: Complete types included out of the box.
- **Tested**: Verified against common edge cases.

## 📦 Installation

```bash
npm install <?= strtolower(preg_replace('/[^a-zA-Z0-9_-]/', '-', $defaultName ?: 'my-project')) ?>
```

## 💡 Usage

```javascript
import { start } from '<?= strtolower(preg_replace('/[^a-zA-Z0-9_-]/', '-', $defaultName ?: 'my-project')) ?>';

start();
```

## 🤝 Contributing

Pull requests are welcome! For major changes, please open an issue first to discuss what you would like to change.

## 📄 License

Distributed under the MIT License. See `LICENSE` for more information.</textarea>
                </div>

                <!-- Rendered Preview Pane -->
                <div class="editor-pane">
                    <div class="pane-toolbar">
                        <span style="font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); text-transform: uppercase;">Preview</span>
                        <span style="font-size: 0.75rem; color: #10B981; font-weight: 600;">GitHub Style</span>
                    </div>
                    <div id="readme-preview-content" class="pane-content markdown-preview"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= ASSETS_URL ?>/js/readme-generator.js?v=<?= APP_VERSION ?>"></script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
