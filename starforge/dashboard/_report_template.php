<?php
/**
 * StarForge - Repository Audit Report Presentation Template
 */
?>
<div class="card" style="border-color: var(--border-bright); padding: 2.25rem;">
    <!-- Fallback Notice Banner -->
    <div id="res-fallback-notice" class="alert alert-info" style="display: none; margin-bottom: 1.5rem; font-size: 0.85rem;"></div>

    <!-- Header Row -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1.5rem; margin-bottom: 2rem; border-bottom: 1px solid var(--border-subtle); padding-bottom: 1.5rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.35rem;">
                <h2 id="res-repo-title" style="font-size: 1.85rem; font-family: var(--font-mono);">owner/repository</h2>
                <a id="res-repo-link" href="#" target="_blank" rel="noopener noreferrer" class="btn btn-secondary btn-sm" style="font-size: 0.75rem;">
                    GitHub ↗
                </a>
            </div>
            <p id="res-repo-desc" style="color: var(--text-secondary); font-size: 1rem; max-width: 650px;">
                Repository description
            </p>
        </div>

        <div style="display: flex; gap: 0.75rem;">
            <a id="res-action-readme" href="<?= BASE_URL ?>/dashboard/readme-generator.php" class="btn btn-primary btn-sm">
                Draft README
            </a>
            <a id="res-action-seo" href="<?= BASE_URL ?>/dashboard/seo-assistant.php" class="btn btn-secondary btn-sm">
                Get Topic Tags
            </a>
        </div>
    </div>

    <!-- Quick Meta Metrics -->
    <div style="display: flex; gap: 2rem; flex-wrap: wrap; margin-bottom: 2.5rem; font-size: 0.875rem; color: var(--text-muted); background: rgba(255, 255, 255, 0.02); padding: 0.85rem 1.25rem; border-radius: var(--radius-md);">
        <div><strong style="color: #fff;">Stars:</strong> <span id="res-stars" style="font-family: var(--font-mono);">0</span></div>
        <div><strong style="color: #fff;">Forks:</strong> <span id="res-forks" style="font-family: var(--font-mono);">0</span></div>
        <div><strong style="color: #fff;">Language:</strong> <span id="res-language">--</span></div>
        <div><strong style="color: #fff;">License:</strong> <span id="res-license" class="tech-badge">MIT</span></div>
    </div>

    <!-- Score Center & 10-Category Breakdown -->
    <div style="display: grid; grid-template-columns: 260px 1fr; gap: 2.5rem; align-items: center; margin-bottom: 3rem;">
        <!-- Left: Overall Score Dial -->
        <div style="text-align: center; border-right: 1px solid var(--border-subtle); padding-right: 2rem;">
            <div class="score-circle-container">
                <svg width="150" height="150" viewBox="0 0 120 120" class="score-circle-svg">
                    <circle cx="60" cy="60" r="50" stroke="rgba(255, 255, 255, 0.08)" stroke-width="8" fill="none"></circle>
                    <circle id="res-score-stroke" cx="60" cy="60" r="50" stroke="url(#scoreGrad)" stroke-width="8" stroke-dasharray="314" stroke-dashoffset="40" stroke-linecap="round" fill="none"></circle>
                    <defs>
                        <linearGradient id="scoreGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#8257FF"></stop>
                            <stop offset="100%" stop-color="#22D3EE"></stop>
                        </linearGradient>
                    </defs>
                </svg>
                <div class="score-text-center">
                    <span id="res-overall-score" class="score-number">--</span>
                    <span class="score-max">/ 100</span>
                </div>
            </div>
            <div id="res-score-tier" class="badge-pill" style="margin-bottom: 0;">Checking...</div>
            <p style="font-size: 0.75rem; color: var(--text-dim); margin-top: 0.5rem;">Internal Quality Score</p>
        </div>

        <!-- Right: 10 Individual Category Bars -->
        <div>
            <h4 style="font-size: 1rem; margin-bottom: 1rem; color: var(--text-secondary);">10-Area Breakdown</h4>
            <div id="res-category-bars"></div>
        </div>
    </div>

    <!-- Strengths & Weaknesses 2-Column Grid -->
    <div class="grid-2" style="margin-bottom: 3rem;">
        <!-- Strengths -->
        <div style="background: rgba(16, 185, 129, 0.04); border: 1px solid rgba(16, 185, 129, 0.2); border-radius: var(--radius-lg); padding: 1.5rem;">
            <h4 style="font-size: 1.05rem; color: #34D399; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <span>✓</span> What looks great
            </h4>
            <ul id="res-strengths-list" style="list-style: none;"></ul>
        </div>

        <!-- Weaknesses -->
        <div style="background: rgba(239, 68, 68, 0.04); border: 1px solid rgba(239, 68, 68, 0.2); border-radius: var(--radius-lg); padding: 1.5rem;">
            <h4 style="font-size: 1.05rem; color: #F87171; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <span>⚠</span> What is missing or needs polish
            </h4>
            <ul id="res-weaknesses-list" style="list-style: none;"></ul>
        </div>
    </div>

    <!-- Priority Recommendations -->
    <div style="margin-bottom: 2.5rem;">
        <h3 style="font-size: 1.3rem; margin-bottom: 1.25rem;">Suggested Fixes (Prioritized)</h3>
        <div id="res-recommendations-list"></div>
    </div>

    <!-- Notice -->
    <div style="padding: 1rem 1.25rem; border-radius: var(--radius-md); background: rgba(255, 255, 255, 0.02); border: 1px solid var(--border-subtle); font-size: 0.8rem; color: var(--text-dim); line-height: 1.5;">
        <strong style="color: var(--text-secondary);">Note:</strong> This score is an internal checklist to help you improve your project. It is not an official GitHub rating or a guarantee of stars.
    </div>
</div>
