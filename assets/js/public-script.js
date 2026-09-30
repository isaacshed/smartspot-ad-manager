/**
 * Public JavaScript for SmartSpot Ad Manager
 */

(function($) {
    'use strict';

    $(document).ready(function() {

        $('.thesiadm-ad-wrapper').addClass('thesiadm-fade-in');

        loadAdSense();

        responsiveAdSizing();

        initAdTracking();

        initLazyLoading();

        $('.thesiadm-close-btn').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).closest('.thesiadm-ad-wrapper').fadeOut();
        });
    });

    function initAdTracking() {
        var trackedAds = [];

        $('.thesiadm-ad-wrapper').each(function() {
            var adId = $(this).data('ad-id');
            if (adId && isInViewport($(this))) {
                trackImpression(adId);
                trackedAds.push(String(adId));
            }
        });

        $('.thesiadm-ad-link').on('click', function(e) {
            var adId = $(this).closest('.thesiadm-ad-wrapper').data('ad-id');
            if (adId) {
                trackClick(adId);
            }
        });

        $(window).on('scroll', debounce(function() {
            $('.thesiadm-ad-wrapper').each(function() {
                var adId = $(this).data('ad-id');
                var key = String(adId);
                if (adId && trackedAds.indexOf(key) === -1 && isInViewport($(this))) {
                    trackImpression(adId);
                    trackedAds.push(key);
                }
            });
        }, 200));
    }

    function trackImpression(adId) {
        $('.thesiadm-ad-wrapper[data-ad-id="' + adId + '"]').attr('data-impression-tracked', 'true');

        if (typeof gtag !== 'undefined') {
            try {
                gtag('event', 'ad_impression', {
                    event_category: 'Ads',
                    event_label: 'Ad ID: ' + adId,
                    value: adId
                });
            } catch (e) {}
        }

        if (typeof ga !== 'undefined') {
            try {
                ga('send', 'event', 'Ads', 'impression', 'Ad ID: ' + adId, adId);
            } catch (e) {}
        }
    }

    function trackClick(adId) {
        if (typeof gtag !== 'undefined') {
            try {
                gtag('event', 'ad_click', {
                    event_category: 'Ads',
                    event_label: 'Ad ID: ' + adId,
                    value: adId
                });
            } catch (e) {}
        }

        if (typeof ga !== 'undefined') {
            try {
                ga('send', 'event', 'Ads', 'click', 'Ad ID: ' + adId, adId);
            } catch (e) {}
        }
    }

    function initLazyLoading() {
        if (!('IntersectionObserver' in window)) {
            return;
        }

        var adObserver = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    var $ad = $(entry.target);
                    $ad.removeClass('thesiadm-loading');
                    adObserver.unobserve(entry.target);
                }
            });
        }, {
            rootMargin: '50px 0px'
        });

        $('.thesiadm-ad-wrapper').each(function() {
            adObserver.observe(this);
        });
    }

    function loadAdSense() {
        var $ads = $('.thesiadm-code-ad ins.adsbygoogle');
        if (!$ads.length) {
            return;
        }

        function pushAds() {
            $ads.each(function() {
                var $ins = $(this);
                if (!$ins.attr('data-adsbygoogle-status')) {
                    try {
                        (adsbygoogle = window.adsbygoogle || []).push({});
                    } catch (e) {}
                }
            });
        }

        if (typeof adsbygoogle === 'undefined' && !$('script[src*="adsbygoogle.js"]').length) {
            var script = document.createElement('script');
            script.async = true;
            script.crossOrigin = 'anonymous';
            script.src = 'https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js';
            script.onload = function() {
                pushAds();
            };
            document.head.appendChild(script);
        } else {
            pushAds();
        }
    }

    function isInViewport($element) {
        if (!$element.length) {
            return false;
        }

        var elementTop = $element.offset().top;
        var elementBottom = elementTop + $element.outerHeight();
        var viewportTop = $(window).scrollTop();
        var viewportBottom = viewportTop + $(window).height();

        return elementBottom > viewportTop && elementTop < viewportBottom;
    }

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

    function responsiveAdSizing() {
        var windowWidth = $(window).width();

        $('.thesiadm-ad-wrapper').each(function() {
            var $ad = $(this);
            var $img = $ad.find('.thesiadm-ad-image');

            if (!$img.length) {
                return;
            }

            var imgWidth = $img.get(0).naturalWidth || $img.width();

            $ad.removeClass('size-728x90 size-300x250 size-160x600 size-300x600 hide-mobile');

            if (imgWidth >= 700) {
                $ad.addClass('size-728x90');
            } else if (imgWidth >= 250 && imgWidth < 350) {
                $ad.addClass('size-300x250');
            } else if (imgWidth < 200) {
                $ad.addClass('size-160x600');
            }

            if (windowWidth < 768 && imgWidth > 500) {
                $ad.addClass('hide-mobile');
            }
        });
    }

    $(window).on('resize', debounce(responsiveAdSizing, 250));

})(jQuery);
