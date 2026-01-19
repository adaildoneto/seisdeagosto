/**
 * Header behavior: categories hide on scroll + mobile search toggle
 */
(function() {
    'use strict';

    function initHeader() {
        var categoriesNav = document.getElementById('categoriesNav');
        var header = document.querySelector('.site-header');
        var contentWrapper = document.getElementById('content');
        var searchToggle = document.getElementById('searchToggleMobile');
        var mobileSearchWrapper = document.getElementById('mobileSearchWrapper');
        var scrollThreshold = 50;
        var ticking = false;

        // Scroll behavior
        function updateScrollState() {
            var scrollTop = window.pageYOffset || document.documentElement.scrollTop;

            if (scrollTop > scrollThreshold) {
                if (header) header.classList.add('scrolled', 'fixed-header');
                if (categoriesNav) categoriesNav.classList.add('hidden');
                if (contentWrapper) contentWrapper.classList.add('header-fixed');
                if (mobileSearchWrapper && searchToggle) {
                    mobileSearchWrapper.classList.remove('active');
                    searchToggle.classList.remove('active');
                }
            } else {
                if (header) header.classList.remove('scrolled', 'fixed-header');
                if (categoriesNav) categoriesNav.classList.remove('hidden');
                if (contentWrapper) contentWrapper.classList.remove('header-fixed');
            }
            ticking = false;
        }

        window.addEventListener('scroll', function() {
            if (!ticking) {
                window.requestAnimationFrame(updateScrollState);
                ticking = true;
            }
        }, { passive: true });

        // Mobile search toggle
        if (searchToggle) {
            searchToggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                if (mobileSearchWrapper) {
                    mobileSearchWrapper.classList.toggle('active');
                }
                searchToggle.classList.toggle('active');
            });

            // Close on outside click
            document.addEventListener('click', function(e) {
                if (searchToggle && mobileSearchWrapper) {
                    if (!searchToggle.contains(e.target) && !mobileSearchWrapper.contains(e.target)) {
                        mobileSearchWrapper.classList.remove('active');
                        searchToggle.classList.remove('active');
                    }
                }
            });

            // Close on blur
            if (mobileSearchWrapper) {
                var searchInput = mobileSearchWrapper.querySelector('input[type="search"]');
                if (searchInput) {
                    searchInput.addEventListener('blur', function() {
                        setTimeout(function() {
                            if (!document.activeElement || !document.activeElement.closest('.navbar-search')) {
                                mobileSearchWrapper.classList.remove('active');
                                searchToggle.classList.remove('active');
                            }
                        }, 200);
                    });
                }
            }
        }

        // Debug log
        console.log('[Header JS] Initialized', {
            categoriesNav: !!categoriesNav,
            header: !!header,
            searchToggle: !!searchToggle,
            mobileSearchWrapper: !!mobileSearchWrapper
        });
    }

    // Init on DOMContentLoaded or immediately if already loaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initHeader);
    } else {
        initHeader();
    }
})();
