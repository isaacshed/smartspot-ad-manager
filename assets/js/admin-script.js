/**
 * Admin JavaScript for SmartSpot Ad Manager
 */

(function($) {
    'use strict';

    $(document).ready(function() {

        $('#thesiadm_ad_type').on('change', function() {
            var adType = $(this).val();

            if (adType === 'code') {
                $('.thesiadm-image-field').slideUp('fast');
            } else {
                $('.thesiadm-image-field').slideDown('fast');
            }
        });

        $('#thesiadm_ad_type').trigger('change');

        $('#thesiadm_target_urls').on('blur', function() {
            var raw = $(this).val();
            if (!raw) {
                return;
            }
            var urls = raw.split('\n');
            var validUrls = [];

            urls.forEach(function(url) {
                url = url.trim();
                if (url && url.length > 0) {
                    if (!url.startsWith('/') && !url.startsWith('http')) {
                        url = '/' + url;
                    }
                    url = url.replace(/\/+$/, '');
                    validUrls.push(url);
                }
            });

            $(this).val(validUrls.join('\n'));
        });

        $('#thesiadm_url_match_type').on('change', function() {
            var matchType = $(this).val();
            var examples = {
                'exact':      'Example: /about-us/ will match only /about-us/',
                'contains':   'Example: /blog/ will match /blog/, /my-blog/, /blog/post-1/, etc.',
                'starts_with':'Example: /blog/ will match /blog/, /blog/post-1/, /blog/category/tech/, etc.'
            };

            var $description = $(this).closest('td').find('.description');

            if (!$description.find('.thesiadm-example-hint').length) {
                $description.append('<br><span class="thesiadm-example-hint" style="color:#2271b1;font-weight:600;"></span>');
            }

            $('.thesiadm-example-hint').text(examples[matchType]);
        });

        $('#thesiadm_ad_priority').on('input change', function() {
            var value = parseInt($(this).val(), 10) || 10;
            var $label = $(this).closest('td').find('.priority-value');

            if (!$label.length) {
                $(this).after('<span class="priority-value" style="margin-left:10px;font-weight:600;color:#2271b1;"></span>');
                $label = $(this).closest('td').find('.priority-value');
            }

            var priorityText = value <= 3 ? 'Highest' : value <= 7 ? 'High' : value <= 15 ? 'Medium' : 'Low';
            $label.text('(' + priorityText + ' Priority)');
        }).trigger('change');

        $('.thesiadm-device-metabox input[type="checkbox"]').on('change', function() {
            var $label = $(this).closest('label');

            if ($(this).is(':checked')) {
                $label.css('background', '#f0f6fc');
            } else {
                $label.css('background', 'transparent');
            }
        }).trigger('change');

        $(document).on('click', '.thesiadm-preview-box code', function() {
            var $code = $(this);
            var text = $code.text();
            var success = false;

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(function() {
                    success = true;
                }).catch(function() {
                    success = false;
                });
            }

            if (!success) {
                try {
                    var $temp = $('<input>');
                    $('body').append($temp);
                    $temp.val(text).select();
                    document.execCommand('copy');
                    $temp.remove();
                    success = true;
                } catch (e) {
                    success = false;
                }
            }

            if (success) {
                var originalBg = $code.css('background-color');
                $code.css('background-color', '#4caf50');

                setTimeout(function() {
                    $code.css('background-color', originalBg);
                }, 300);

                var $tooltip = $('<span class="thesiadm-tooltip" style="display:inline-block;margin-left:6px;padding:2px 6px;background:#1d2327;color:#fff;font-size:11px;border-radius:3px;">Copied!</span>');
                $code.after($tooltip);

                setTimeout(function() {
                    $tooltip.fadeOut(function() {
                        $(this).remove();
                    });
                }, 1500);
            }
        });

        $('#thesiadm_ad_code').on('blur', function() {
            var code = $(this).val();
            var $field = $(this);

            $field.closest('.thesiadm-metabox').find('.thesiadm-validation-msg').remove();

            if (!code || !code.trim()) {
                return;
            }

            var msg = '';
            if (code.indexOf('adsbygoogle') !== -1 || code.indexOf('data-ad-client') !== -1) {
                msg = '<p class="thesiadm-validation-msg" style="color:#388e3c;margin-top:8px;">&#10003; Google AdSense code detected.</p>';
            } else if (code.indexOf('<script') !== -1 || code.indexOf('<iframe') !== -1 || code.indexOf('<ins') !== -1) {
                msg = '<p class="thesiadm-validation-msg" style="color:#2271b1;margin-top:8px;">&#10003; Custom ad code detected.</p>';
            }

            if (msg) {
                $field.after(msg);
            }
        });

        var formChanged = false;
        $('.thesiadm-metabox input, .thesiadm-metabox textarea, .thesiadm-metabox select').on('change input', function() {
            formChanged = true;
        });

        $(window).on('beforeunload', function() {
            if (formChanged) {
                return 'You have unsaved changes. Are you sure you want to leave?';
            }
        });

        $('form#post').on('submit', function() {
            formChanged = false;
        });

        if ($('.post-type-thesiadm_ad .wp-list-table').length) {
            var totalAds = $('.post-type-thesiadm_ad .wp-list-table tbody tr').not('.no-items').length;

            if (totalAds > 0) {
                var $stats = $('<div class="thesiadm-quick-stats" style="background:#f0f6fc;padding:12px;margin:15px 0;border-radius:4px;border-left:4px solid #2271b1;"></div>');
                $stats.html('<strong>Quick Stats:</strong> ' + totalAds + ' ad' + (totalAds !== 1 ? 's' : '') + ' configured.');
                $('.post-type-thesiadm_ad .wp-header-end').after($stats);
            }
        }

        if ($('.post-type-thesiadm_ad #postimagediv').length) {
            var $imageDiv = $('#postimagediv');
            var $adType = $('#thesiadm_ad_type');

            function updateFeaturedImageVisibility() {
                if ($adType.val() === 'image') {
                    $imageDiv.slideDown();
                    if (!$imageDiv.find('.thesiadm-featured-hint').length) {
                        $imageDiv.find('.inside').prepend('<p class="thesiadm-featured-hint description" style="background:#fff3e0;padding:8px;border-radius:4px;margin-bottom:10px;">Upload your ad banner image here. Common sizes: 728&times;90, 300&times;250, 160&times;600.</p>');
                    }
                } else {
                    $imageDiv.slideUp();
                }
            }

            $adType.on('change', updateFeaturedImageVisibility);
            updateFeaturedImageVisibility();
        }

        $(document).on('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && (e.key === 's' || e.which === 83)) {
                e.preventDefault();
                var $publish = $('#publish');
                var $save = $('#save-post');
                if ($publish.length) {
                    $publish.click();
                } else if ($save.length) {
                    $save.click();
                }
            }
        });

        var helpTexts = {
            'Ad Position': 'Select where this ad appears on your site. Use shortcodes or template tags for custom positions.',
            'Ad Type': 'Image ads use the Featured Image. Custom Code supports Google AdSense, HTML, and JavaScript.',
            'Priority': 'When multiple ads match the same page, lower numbers show first. Range: 1-100.',
            'Target URLs': 'One URL per line (relative paths work best, e.g. /blog/).'
        };

        $('.thesiadm-metabox th label').each(function() {
            var labelText = $(this).text().trim();
            if (helpTexts[labelText]) {
                $(this).attr('title', helpTexts[labelText]).css('cursor', 'help');
            }
        });
    });

})(jQuery);
