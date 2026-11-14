/**
 * StarForge - Main Interactive JavaScript
 */

document.addEventListener('DOMContentLoaded', () => {
    initToasts();
    initCopyButtons();
    initFaqAccordion();
    initMobileNav();
});

// Toast notification helper
function showToast(message, type = 'info') {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    
    let icon = '✦';
    if (type === 'success') icon = '✓';
    if (type === 'error') icon = '⚠';

    toast.innerHTML = `<span style="color: var(--accent-cyan); font-weight: bold;">${icon}</span> <span>${message}</span>`;
    container.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(10px)';
        toast.style.transition = 'all 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3500);
}

function initToasts() {
    window.showToast = showToast;
}

// Copy to clipboard helper
function initCopyButtons() {
    document.addEventListener('click', (e) => {
        const copyBtn = e.target.closest('[data-copy]');
        if (copyBtn) {
            const textToCopy = copyBtn.getAttribute('data-copy') || '';
            if (!textToCopy) return;

            navigator.clipboard.writeText(textToCopy).then(() => {
                const originalHtml = copyBtn.innerHTML;
                copyBtn.innerHTML = '✓ Copied!';
                copyBtn.style.borderColor = 'var(--accent-cyan)';
                showToast('Copied to clipboard!', 'success');
                setTimeout(() => {
                    copyBtn.innerHTML = originalHtml;
                    copyBtn.style.borderColor = '';
                }, 2000);
            }).catch(() => {
                showToast('Failed to copy to clipboard', 'error');
            });
        }
    });
}

// FAQ Accordions
function initFaqAccordion() {
    const questions = document.querySelectorAll('.faq-question');
    questions.forEach(q => {
        q.addEventListener('click', () => {
            const item = q.parentElement;
            const wasActive = item.classList.contains('active');
            document.querySelectorAll('.faq-item').forEach(i => i.classList.remove('active'));
            if (!wasActive) {
                item.classList.add('active');
            }
        });
    });
}

// Mobile Nav Toggle
function initMobileNav() {
    const toggle = document.querySelector('.nav-mobile-toggle');
    const links = document.querySelector('.nav-links');
    if (toggle && links) {
        toggle.addEventListener('click', () => {
            const isShown = links.style.display === 'flex';
            links.style.display = isShown ? 'none' : 'flex';
            links.style.flexDirection = 'column';
            links.style.position = 'absolute';
            links.style.top = '100%';
            links.style.left = '0';
            links.style.right = '0';
            links.style.background = 'rgba(8, 11, 20, 0.98)';
            links.style.padding = '1.5rem';
            links.style.borderBottom = '1px solid var(--border-subtle)';
        });
    }
}
