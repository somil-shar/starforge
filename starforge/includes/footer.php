<?php
/**
 * StarForge - Footer Layout Component
 */
?>
        <!-- Footer Component -->
        <footer class="footer">
            <div class="container">
                <div class="footer-grid" style="grid-template-columns: 2fr 1fr 1fr;">
                    <div class="footer-col">
                        <div class="brand-logo" style="margin-bottom: 1rem;">
                            <div class="brand-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                </svg>
                            </div>
                            <span>STARFORGE</span>
                        </div>
                        <p style="color: var(--text-muted); font-size: 0.9rem; max-width: 340px; margin-bottom: 1rem; line-height: 1.6;">
                            Simple, practical checks and documentation tools to help developers publish repos people can actually install, understand, and use.
                        </p>
                        <div style="display: inline-block; padding: 0.35rem 0.75rem; border-radius: var(--radius-sm); background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.25); color: #34D399; font-size: 0.75rem; font-weight: 600;">
                            ✓ Genuine developer recognition only
                        </div>
                    </div>

                    <div class="footer-col">
                        <h4>Tools</h4>
                        <ul class="footer-links">
                            <li><a href="<?= BASE_URL ?>/dashboard/analyzer.php">Repository Analyzer</a></li>
                            <li><a href="<?= BASE_URL ?>/dashboard/readme-generator.php">README Generator</a></li>
                            <li><a href="<?= BASE_URL ?>/dashboard/seo-assistant.php">GitHub SEO Optimizer</a></li>
                        </ul>
                    </div>

                    <div class="footer-col">
                        <h4>Our Promise</h4>
                        <p style="color: var(--text-muted); font-size: 0.825rem; line-height: 1.6; margin-bottom: 0.75rem;">
                            StarForge never sells stars, runs engagement bots, or supports follow-for-follow rings. Real stars come when people try your project and it works.
                        </p>
                        <span style="color: var(--text-dim); font-size: 0.75rem;">Not affiliated with GitHub, Inc.</span>
                    </div>
                </div>

                <div class="footer-bottom">
                    <div>
                        &copy; <?= date('Y') ?> StarForge. Built for open source developers.
                    </div>
                    <div style="display: flex; gap: 1.5rem;">
                        <a href="https://docs.github.com/en/rest" target="_blank" rel="noopener noreferrer" style="color: var(--text-muted);">Powered by GitHub REST API</a>
                        <a href="#top" style="color: var(--accent-cyan);">Back to top ↑</a>
                    </div>
                </div>
            </div>
        </footer>
    </div><!-- End .app-wrapper -->

    <div id="toast-container"></div>

    <!-- Scripts -->
    <script src="<?= ASSETS_URL ?>/js/main.js?v=<?= APP_VERSION ?>"></script>
    <script src="<?= ASSETS_URL ?>/js/analyzer.js?v=<?= APP_VERSION ?>"></script>
</body>
</html>
