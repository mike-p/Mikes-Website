// Inline Navigation - Fixed positioning and active state on scroll
(function() {
    'use strict';

    const inlineNav = document.querySelector('.inline-nav');
    if (!inlineNav) {
        return; // Exit if no inline nav found
    }

    const navItems = document.querySelectorAll('.inline-nav-item');
    const sections = document.querySelectorAll('.content-section');

    if (navItems.length === 0 || sections.length === 0) {
        return; // Exit if no navigation items or sections found
    }

    // Shared throttle function
    function throttle(func, wait) {
        let timeout;
        return function() {
            if (!timeout) {
                timeout = setTimeout(function() {
                    timeout = null;
                    func();
                }, wait);
            }
        };
    }

    // ===== Fixed positioning on desktop =====
    let navOffsetTop = 0;
    let isFixed = false;
    const isDesktop = window.innerWidth >= 1025;

    function updateNavPosition() {
        // Only apply fixed positioning on desktop
        if (!isDesktop) {
            return;
        }

        if (!navOffsetTop) {
            // Get the initial offset position
            navOffsetTop = inlineNav.offsetTop;
        }

        const scrollY = window.scrollY;

        // If scrolled past the nav's original position, make it fixed at bottom
        if (scrollY >= navOffsetTop && !isFixed) {
            inlineNav.classList.add('inline-nav--fixed');
            isFixed = true;
        } else if (scrollY < navOffsetTop && isFixed) {
            inlineNav.classList.remove('inline-nav--fixed');
            isFixed = false;
        }
    }

    // ===== Active state highlighting =====
    function updateActiveNav() {
        let currentSection = '';
        const scrollPosition = window.scrollY + window.innerHeight / 3;

        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.offsetHeight;

            if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
                currentSection = section.getAttribute('id');
            }
        });

        navItems.forEach(item => {
            item.classList.remove('active');
            const href = item.getAttribute('href');
            if (href === '#' + currentSection) {
                item.classList.add('active');
            }
        });
    }

    // ===== Combined scroll handler =====
    function handleScroll() {
        updateNavPosition();
        updateActiveNav();
    }

    // Listen to scroll events (throttled)
    window.addEventListener('scroll', throttle(handleScroll, 10));
    
    // Listen to resize to recalculate offset
    window.addEventListener('resize', function() {
        if (isDesktop && inlineNav) {
            navOffsetTop = inlineNav.offsetTop;
            updateNavPosition();
        }
    });

    // Initial checks
    updateNavPosition();
    updateActiveNav();
})();
