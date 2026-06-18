/**
 * Kopizon HTML Includes Loader
 * Fetches partial HTML from includes/ and injects into [data-include] placeholders.
 * Must be loaded BEFORE kopizon-features.js.
 *
 * Automatically resolves paths relative to the theme directory,
 * regardless of the URL the page is served at.
 *
 * Usage in HTML:
 *   <div data-include="topbar"></div>
 *   <div data-include="header"></div>
 *   ... page content ...
 *   <div data-include="footer-cta"></div>
 *   <div data-include="footer"></div>
 *   <div data-include="mobile-drawer"></div>
 *   <script src="assets/js/kopizon-includes.js"></script>
 *   <script src="assets/js/kopizon-features.js"></script>
 */

(function () {
    'use strict';

    // Resolve the theme base path from this script's src attribute
    // Script is at: assets/js/kopizon-includes.js
    // Theme root is two directories up from the script
    var scripts = document.getElementsByTagName('script');
    var currentScript = scripts[scripts.length - 1]; // last script = this one (synchronous)
    var scriptSrc = currentScript.getAttribute('src') || '';
    var basePath = scriptSrc.replace(/assets\/js\/kopizon-includes\.js.*$/, '');

    var includes = document.querySelectorAll('[data-include]');
    var pending = includes.length;

    if (pending === 0) return;

    includes.forEach(function (el) {
        var name = el.getAttribute('data-include');
        var path = basePath + 'includes/' + name + '.html';

        fetch(path)
            .then(function (res) {
                if (!res.ok) throw new Error('Include not found: ' + path + ' (HTTP ' + res.status + ')');
                return res.text();
            })
            .then(function (html) {
                // Replace placeholder with actual content
                el.outerHTML = html;
            })
            .catch(function (err) {
                console.warn('[Kopizon Includes]', err.message);
            })
            .finally(function () {
                pending--;
                if (pending === 0) {
                    // All includes loaded — dispatch event so features.js can initialise
                    document.dispatchEvent(new Event('includes-loaded'));
                }
            });
    });
})();
