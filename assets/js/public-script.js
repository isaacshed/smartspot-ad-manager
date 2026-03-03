/**
 * Public JavaScript for SmartSpot Ad Manager
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        
        // Initialize ad tracking
        initAdTracking();
        
        // Initialize lazy loading for ads
        initLazyLoading();
        
        // Add fade-in animation
        $('.thesiadm-ad-wrapper').addClass('thesiadm-fade-in');
        
        // AdSense auto-load
        loadAdSense();
        
        // Responsive ad sizing
        responsiveAdSizing();
        
    });
    
    /**
     * Initialize ad impression and click tracking
     */
    function initAdTracking() {
        // Track impressions
        $('.thesiadm-ad-wrapper').each(function() {
            var adId = $(this).data('ad-id');
            
            if (adId && isInViewport($(this))) {
                trackImpression(adId);
            }
        });
        
        // Track clicks
        $('.thesiadm-ad-link').on('click', function(e) {
            var adId = $(this).closest('.thesiadm-ad-wrapper').data('ad-id');
            
            if (adId) {
                trackClick(adId);
            }
        });
        
        // Track impressions on scroll (for lazy loaded ads)
        var trackedAds = [];
        
        $(window).on('scroll', debounce(function() {
            $('.thesiadm-ad-wrapper').each(function() {
                var adId = $(this).data('ad-id');
                
                if (adId && trackedAds.indexOf(adId) === -1 && isInViewport($(this))) {
                    trackImpression(adId);
                    trackedAds.push(adId);
                }
            });
        }, 200));
    }
    
    /**
     * Track ad impression
     */
    function trackImpression(adId) {
        // Mark as tracked
        $('.thesiadm-ad-wrapper[data-ad-id="' + adId + '"]').attr('data-impression-tracked', 'true');
        
        // Send to Google Analytics if available
        if (typeof gtag !== 'undefined') {
            gtag('event', 'ad_impression', {
                'event_category': 'Ads',
                'event_label': 'Ad ID: ' + adId,
                'value': adId
            });
        }
        
        // Alternative: Google Analytics Universal
        if (typeof ga !== 'undefined') {
            ga('send', 'event', 'Ads', 'impression', 'Ad ID: ' + adId, adId);
        }
    }
    
    /**
     * Track ad click
     */
    function trackClick(adId) {
        // Send to Google Analytics if available
        if (typeof gtag !== 'undefined') {
            gtag('event', 'ad_click', {
                'event_category': 'Ads',
                'event_label': 'Ad ID: ' + adId,
                'value': adId
            });
        }
        
        // Alternative: Google Analytics Universal
        if (typeof ga !== 'undefined') {
            ga('send', 'event', 'Ads', 'click', 'Ad ID: ' + adId, adId);
        }
    }
    
    /**
     * Initialize lazy loading for ads
     */
    function initLazyLoading() {
        if ('IntersectionObserver' in window) {
            var adObserver = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        var $ad = $(entry.target);
                        $ad.removeClass('thesiadm-loading');
                        adObserver.unobserve(entry.target);
                    }
                });
            }, {
                rootMargin: '50px'
            });
            
            $('.thesiadm-ad-wrapper').each(function() {
                adObserver.observe(this);
            });
        }
    }
    
    /**
     * Load AdSense script if needed
     */
    function loadAdSense() {
        // Check if there are any AdSense ads
        if ($('.thesiadm-code-ad ins.adsbygoogle').length > 0) {
            // Check if AdSense script is already loaded
            if (typeof adsbygoogle === 'undefined') {
                // Load AdSense script dynamically
                var script = document.createElement('script');
                script.async = true;
                script.src = 'https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js';
                script.crossOrigin = 'anonymous';
                document.head.appendChild(script);
                
                script.onload = function() {
                    // Push all AdSense ads
                    $('.thesiadm-code-ad ins.adsbygoogle').each(function() {
                        if (!$(this).attr('data-adsbygoogle-status')) {
                            (adsbygoogle = window.adsbygoogle || []).push({});
                        }
                    });
                };
            } else {
                // AdSense already loaded, just push the ads
                $('.thesiadm-code-ad ins.adsbygoogle').each(function() {
                    if (!$(this).attr('data-adsbygoogle-status')) {
                        (adsbygoogle = window.adsbygoogle || []).push({});
                    }
                });
            }
        }
    }
    
    /**
     * Check if element is in viewport
     */
    function isInViewport($element) {
        if (!$element.length) return false;
        
        var elementTop = $element.offset().top;
        var elementBottom = elementTop + $element.outerHeight();
        var viewportTop = $(window).scrollTop();
        var viewportBottom = viewportTop + $(window).height();
        
        return elementBottom > viewportTop && elementTop < viewportBottom;
    }
    
    /**
     * Debounce function
     */
    function debounce(func, wait) {
        var timeout;
        return function() {
            var context = this;
            var args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(function() {
                func.apply(context, args);
            }, wait);
        };
    }
    
    /**
     * Responsive ad sizing
     */
    function responsiveAdSizing() {
        var windowWidth = $(window).width();
        
        $('.thesiadm-ad-wrapper').each(function() {
            var $ad = $(this);
            var $img = $ad.find('.thesiadm-ad-image');
            
            if ($img.length) {
                var imgWidth = $img.width();
                
                // Add size class based on image dimensions
                if (imgWidth >= 700) {
                    $ad.addClass('size-728x90');
                } else if (imgWidth >= 250 && imgWidth < 350) {
                    $ad.addClass('size-300x250');
                } else if (imgWidth < 200) {
                    $ad.addClass('size-160x600');
                }
                
                // Hide large ads on mobile
                if (windowWidth < 768 && imgWidth > 500) {
                    $ad.addClass('hide-mobile');
                }
            }
        });
    }
    
    // Run responsive sizing on window resize
    $(window).on('resize', debounce(responsiveAdSizing, 250));
    
})(jQuery);
