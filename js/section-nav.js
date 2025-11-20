// Section Navigation Active State on Scroll
(function() {
    'use strict';

    // Get all section nav items and sections
    const nav = document.querySelector('.section-nav');
    const navItems = document.querySelectorAll('.section-nav-item');
    const sections = document.querySelectorAll('.content-section');

    if (navItems.length === 0 || sections.length === 0) {
        return; // Exit if no navigation or sections found
    }

    // Mobile navigation - no toggle needed, always visible

    // Function to update active nav item
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

    // Throttle function to limit scroll event firing
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

    // Listen to scroll events (throttled)
    window.addEventListener('scroll', throttle(updateActiveNav, 100));

    // Initial check
    updateActiveNav();
})();

