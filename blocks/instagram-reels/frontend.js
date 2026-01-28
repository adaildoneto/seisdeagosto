/**
 * Instagram Reels Gallery - Frontend Script
 * Handles image loading errors and retries
 */

(function() {
    'use strict';

    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    function init() {
        handleImageErrors();
    }

    /**
     * Handle image loading errors with retry and fallback
     */
    function handleImageErrors() {
        const images = document.querySelectorAll('.ig-reel-thumbnail img');
        
        images.forEach(function(img) {
            // Track retry attempts
            img.retryCount = img.retryCount || 0;
            
            // Add load event to remove placeholder
            img.addEventListener('load', function() {
                img.parentElement.classList.add('loaded');
            });
            
            // Add error event with retry logic
            img.addEventListener('error', function() {
                console.warn('Instagram image failed to load:', img.src);
                
                // After error, show fallback immediately
                console.error('Failed to load Instagram image');
                img.parentElement.classList.add('error');
                createFallback(img);
            });
        });
    }

    /**
     * Create fallback placeholder for failed images
     */
    function createFallback(img) {
        const container = img.parentElement;
        const fallback = document.createElement('div');
        fallback.className = 'ig-image-fallback';
        fallback.innerHTML = '<i class="fa fa-instagram"></i><p>Imagem não disponível</p>';
        
        // Hide original image
        img.style.display = 'none';
        
        // Add fallback
        container.appendChild(fallback);
    }

    /**
     * Optimize image URLs by removing unnecessary parameters
     */
    function optimizeImageUrl(url) {
        try {
            const urlObj = new URL(url);
            // Keep only essential parameters
            const essentialParams = ['_nc_cat', '_nc_ohc'];
            const newParams = new URLSearchParams();
            
            essentialParams.forEach(function(param) {
                if (urlObj.searchParams.has(param)) {
                    newParams.set(param, urlObj.searchParams.get(param));
                }
            });
            
            return urlObj.origin + urlObj.pathname + (newParams.toString() ? '?' + newParams.toString() : '');
        } catch (e) {
            return url;
        }
    }

})();
