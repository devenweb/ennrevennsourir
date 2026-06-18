/**
 * Kopizon Theme Features (ported from Qizon)
 * - Page Loader
 * - Sticky Header
 * - Mobile Hamburger Menu
 * - Back to Top Button
 *
 * Works in two modes:
 * 1. With kopizon-includes.js → waits for 'includes-loaded' event
 * 2. Without includes (inline HTML) → runs on DOMContentLoaded
 */

(function () {
    'use strict';

    function init() {
        console.log('[Kopizon] Features JS Loaded');

        // ====== PAGE LOADER ======
        var loader = document.getElementById('page-loader');
        if (loader) {
            console.log('[Kopizon] Hiding loader');
            setTimeout(function () {
                loader.classList.add('loaded');
            }, 300);
        }

        // ====== STICKY HEADER ======
        var header = document.querySelector('.mockup-header');
        var topBar = document.querySelector('.mockup-top-bar');

        if (header) {
            console.log('[Kopizon] Sticky tracker active');
            var handleScroll = function () {
                var scrollPos = window.pageYOffset || document.documentElement.scrollTop;
                var topBarHeight = topBar ? topBar.offsetHeight : 0;
                var stickyPoint = topBarHeight > 0 ? topBarHeight : 80; // Fallback to 80px

                if (scrollPos > stickyPoint) {
                    if (!header.classList.contains('is-sticky')) {
                        console.log('[Kopizon] Sticky ON (Scroll: ' + scrollPos + ', Point: ' + stickyPoint + ')');
                        header.classList.add('is-sticky');
                        document.body.classList.add('header-sticky-active');
                    }
                } else {
                    if (header.classList.contains('is-sticky')) {
                        console.log('[Kopizon] Sticky OFF');
                        header.classList.remove('is-sticky');
                        document.body.classList.remove('header-sticky-active');
                    }
                }
            };

            window.addEventListener('scroll', handleScroll, { passive: true });
            handleScroll(); // Initial check
        } else {
            console.warn('[Kopizon] Header (.mockup-header) not found for sticky logic');
        }

        // ====== MOBILE MENU ======
        var hamburger = document.getElementById('hamburger-btn');
        var drawer = document.getElementById('mobile-drawer');
        var overlay = document.getElementById('mobile-overlay');
        var closeBtn = document.getElementById('close-drawer');

        function openMenu() {
            if (hamburger) hamburger.classList.add('active');
            if (drawer) drawer.classList.add('active');
            if (overlay) overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        function closeMenu() {
            if (hamburger) hamburger.classList.remove('active');
            if (drawer) drawer.classList.remove('active');
            if (overlay) overlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        if (hamburger) hamburger.addEventListener('click', openMenu);
        if (closeBtn) closeBtn.addEventListener('click', closeMenu);
        if (overlay) overlay.addEventListener('click', closeMenu);

        // Sub-menu toggles for mobile
        document.querySelectorAll('.mobile-nav-drawer .menu-item-has-children > a').forEach(function (link) {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                var sub = this.nextElementSibling;
                if (sub && sub.classList.contains('sub-menu')) {
                    sub.classList.toggle('open');
                    this.parentElement.classList.toggle('active');
                }
            });
        });

        // ====== BACK TO TOP ======
        var backToTop = document.getElementById('back-to-top');
        if (backToTop) {
            window.addEventListener('scroll', function () {
                if (window.pageYOffset > 400) {
                    backToTop.classList.add('visible');
                } else {
                    backToTop.classList.remove('visible');
                }
            });
            backToTop.addEventListener('click', function () {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        }

        // ====== SWIPER SLIDERS ======
        if (typeof Swiper !== 'undefined') {
            new Swiper('.testimonial-slider', {
                slidesPerView: 1,
                spaceBetween: 30,
                loop: true,
                pagination: { el: '.swiper-pagination', clickable: true },
                breakpoints: {
                    991: { slidesPerView: 2 }
                }
            });

            new Swiper('.news-slider', {
                slidesPerView: 1,
                spaceBetween: 30,
                loop: true,
                pagination: { el: '.swiper-pagination', clickable: true },
                breakpoints: {
                    768: { slidesPerView: 2 },
                    991: { slidesPerView: 3 }
                }
            });
        }

        // ====== HERO ANIMATIONS ======
        var heroElement = document.getElementById('hero-slider');
        var heroTitleElement = document.getElementById('hero-title');
        if (heroElement && heroTitleElement) {
            var templateUrl = window.kopizonData ? window.kopizonData.templateUrl : '';
            var slides = window.kopizonData && window.kopizonData.heroSlides ? window.kopizonData.heroSlides : [
                {
                    image: templateUrl + '/assets/images/hero-bg.jpg',
                    title: 'A Dream and a Smile for every child'
                },
                {
                    image: templateUrl + '/assets/images/cancer-care.jpg',
                    title: 'Specialized Care for Pediatric Cancer'
                },
                {
                    image: templateUrl + '/assets/images/heart-surgery.jpg',
                    title: 'Saving Lives Through Heart Surgery'
                }
            ];

            var currentIndex = 0;

            function animateTitle(text) {
                if (!text) return;
                heroTitleElement.innerHTML = '';
                text.split('').forEach(function (char, i) {
                    var span = document.createElement('span');
                    span.className = 'char';
                    span.textContent = char === ' ' ? '\u00A0' : char;
                    span.style.animationDelay = (i * 0.03) + 's';
                    heroTitleElement.appendChild(span);
                });
            }

            // Initial state
            heroElement.style.backgroundImage = "url('" + slides[0].image + "')";
            animateTitle(slides[0].title);

            if (slides.length > 1) {
                setInterval(function () {
                    currentIndex = (currentIndex + 1) % slides.length;
                    heroElement.style.backgroundImage = "url('" + slides[currentIndex].image + "')";
                    animateTitle(slides[currentIndex].title);
                }, 6000);
            }
        }

        // ====== COUNTER ANIMATION ======
        var counterSection = document.querySelector('.counter-section');
        if (counterSection) {
            var counted = false;
            var observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting && !counted) {
                        counted = true;
                        document.querySelectorAll('.counter-number').forEach(function (el) {
                            var target = parseInt(el.getAttribute('data-target'), 10);
                            var duration = 2000;
                            var start = 0;
                            var startTime = null;

                            function animate(timestamp) {
                                if (!startTime) startTime = timestamp;
                                var progress = Math.min((timestamp - startTime) / duration, 1);
                                // Ease-out cubic
                                var eased = 1 - Math.pow(1 - progress, 3);
                                el.textContent = Math.floor(eased * target);
                                if (progress < 1) {
                                    requestAnimationFrame(animate);
                                } else {
                                    el.textContent = target;
                                }
                            }
                            requestAnimationFrame(animate);
                        });
                    }
                });
            }, { threshold: 0.3 });
            observer.observe(counterSection);
        }
    }

    // Reliable Initialization
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    // Fallback for includes-loaded
    document.addEventListener('includes-loaded', init);
})();
