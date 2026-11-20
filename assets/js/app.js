/**
 * Recipe Explorer — Vanilla JavaScript
 * 
 * Handles mobile navigation toggle and minor UI enhancements.
 */

document.addEventListener('DOMContentLoaded', function () {

    // ── Mobile Navigation Toggle ──
    const navToggle = document.getElementById('nav-toggle');
    const siteNav = document.getElementById('site-nav');

    if (navToggle && siteNav) {
        navToggle.addEventListener('click', function () {
            siteNav.classList.toggle('open');
            navToggle.classList.toggle('open');
            const isOpen = siteNav.classList.contains('open');
            navToggle.setAttribute('aria-expanded', isOpen);
        });

        // Close nav when clicking outside
        document.addEventListener('click', function (e) {
            if (!siteNav.contains(e.target) && !navToggle.contains(e.target)) {
                siteNav.classList.remove('open');
                navToggle.classList.remove('open');
                navToggle.setAttribute('aria-expanded', false);
            }
        });
    }

    // ── Lazy image error handling ──
    document.querySelectorAll('img').forEach(function (img) {
        img.addEventListener('error', function () {
            if (!this.dataset.fallback) {
                this.dataset.fallback = '1';
                this.src = 'data:image/svg+xml,' + encodeURIComponent(
                    '<svg xmlns="http://www.w3.org/2000/svg" width="400" height="300" fill="%23f0f0f0">' +
                    '<rect width="400" height="300"/>' +
                    '<text x="200" y="150" text-anchor="middle" fill="%23999" font-size="18" font-family="sans-serif">Image unavailable</text>' +
                    '</svg>'
                );
                this.alt = 'Image unavailable';
            }
        });
    });

    // ── Smooth scroll for anchor links ──
    document.querySelectorAll('a[href^="#"]').forEach(function (link) {
        link.addEventListener('click', function (e) {
            var target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
});
