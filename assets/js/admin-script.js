/**
 * Admin JavaScript for SmartSpot Ad Manager
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        
        // Toggle image fields based on ad type
        $('#thesiadm_ad_type').on('change', function() {
            var adType = $(this).val();
            
            if (adType === 'code') {
                $('.thesiadm-image-field').slideUp('fast');
            } else {
                $('.thesiadm-image-field').slideDown('fast');
            }
        });
        
        // Trigger on page load
        $('#thesiadm_ad_type').trigger('change');
        
        // URL validation and formatting
        $('#thesiadm_target_urls').on('blur', function() {
            var urls = $(this).val().split('\n');
            var validUrls = [];
            
            urls.forEach(function(url) {
                url = url.trim();
                if (url && url.length > 0) {
                    // Ensure URL starts with /
                    if (!url.startsWith('/')) {
                        url = '/' + url;
                    }
                    // Remove trailing slash for consistency (we'll add it back in PHP)
                    url = url.replace(/\/+$/, '');
                    validUrls.push(url);
                }
            });
            
            $(this).val(validUrls.join('\n'));
        });
        
        // Add placeholder text on focus
        $('#thesiadm_target_urls').on('focus', function() {
            if ($(this).val() === '') {
                $(this).attr('placeholder', '/\n/blog/\n/products/\n/contact-us/');
            }
        });
        
        // Show URL match type examples dynamically
        $('#thesiadm_url_match_type').on('change', function() {
            var matchType = $(this).val();
            var examples = {
                'exact': 'Example: /about-us/ will match only /about-us/',
                'contains': 'Example: /blog/ will match /blog/, /my-blog/, /blog/post-1/, etc.',
                'starts_with': 'Example: /blog/ will match /blog/, /blog/post-1/, /blog/category/tech/, etc.'
            };
            
            var $description = $(this).closest('td').find('.description');
            
            // Add dynamic example hint
            if (!$description.find('.thesiadm-example-hint').length) {
                $description.append('<br><span class="thesiadm-example-hint" style="color: #2271b1; font-weight: 600;"></span>');
            }
            
            $('.thesiadm-example-hint').text(examples[matchType]);
        });
        
        // Priority slider visual feedback
        $('#thesiadm_ad_priority').on('input change', function() {
            var value = $(this).val();
            var $label = $(this).closest('td').find('.priority-value');
            
            if (!$label.length) {
                $(this).after('<span class="priority-value" style="margin-left: 10px; font-weight: 600; color: #2271b1;"></span>');
                $label = $(this).closest('td').find('.priority-value');
            }
            
            var priorityText = value <= 3 ? 'Highest' : value <= 7 ? 'High' : value <= 15 ? 'Medium' : 'Low';
            $label.text('(' + priorityText + ' Priority)');
        }).trigger('change');
        
        // Device targeting - visual feedback
        $('.thesiadm-device-metabox input[type="checkbox"]').on('change', function() {
            var $label = $(this).closest('label');
            
            if ($(this).is(':checked')) {
                $label.css('background', '#f0f6fc');
            } else {
                $label.css('background', 'transparent');
            }
            
            // Ensure at least one device is selected
            var checkedCount = $('.thesiadm-device-metabox input[type="checkbox"]:checked').length;
            if (checkedCount === 0) {
                alert('Please select at least one device. All devices will be selected by default if none are chosen.');
            }
        }).trigger('change');
        
        // Copy shortcode to clipboard
        $(document).on('click', '.thesiadm-preview-box code', function() {
            var $code = $(this);
            var text = $code.text();
            
            // Create temporary input
            var $temp = $('<input>');
            $('body').append($temp);
            $temp.val(text).select();
            document.execCommand('copy');
            $temp.remove();
            
            // Visual feedback
            var originalBg = $code.css('background-color');
            $code.css('background-color', '#4caf50');
            
            setTimeout(function() {
                $code.css('background-color', originalBg);
            }, 300);
            
            // Show tooltip
            var $tooltip = $('<span class="thesiadm-tooltip">Copied!</span>');
            $code.after($tooltip);
            
            setTimeout(function() {
                $tooltip.fadeOut(function() {
                    $(this).remove();
                });
            }, 1500);
        });
        
        // Ad code validation for AdSense
        $('#thesiadm_ad_code').on('blur', function() {
            var code = $(this).val();
            var $field = $(this);
            
            // Remove any existing validation messages
            $field.closest('td').find('.thesiadm-validation-msg').remove();
            
            if (code.trim() === '') {
                return;
            }
            
            // Check if it looks like AdSense code
            if (code.indexOf('adsbygoogle') !== -1 || code.indexOf('data-ad-client') !== -1) {
                var msg = '<p class="thesiadm-validation-msg" style="color: #388e3c; margin-top: 8px;">✓ Google AdSense code detected</p>';
                $field.after(msg);
            } else if (code.indexOf('<script') !== -1 || code.indexOf('<iframe') !== -1) {
                var msg = '<p class="thesiadm-validation-msg" style="color: #2271b1; margin-top: 8px;">✓ Custom ad code detected</p>';
                $field.after(msg);
            }
        });
        
        // Auto-save warning
        var formChanged = false;
        $('.thesiadm-metabox input, .thesiadm-metabox textarea, .thesiadm-metabox select').on('change', function() {
            formChanged = true;
        });
        
        $(window).on('beforeunload', function() {
            if (formChanged) {
                return 'You have unsaved changes. Are you sure you want to leave?';
            }
        });
        
        // Clear warning on form submit
        $('form#post').on('submit', function() {
            formChanged = false;
        });
        
        // Quick stats in admin list
        if ($('.post-type-thesiadm_ad .wp-list-table').length) {
            var totalAds = $('.post-type-thesiadm_ad .wp-list-table tbody tr').not('.no-items').length;
            
            if (totalAds > 0) {
                var $stats = $('<div class="thesiadm-quick-stats" style="background: #f0f6fc; padding: 12px; margin: 15px 0; border-radius: 4px; border-left: 4px solid #2271b1;"></div>');
                $stats.html('<strong>Quick Stats:</strong> ' + totalAds + ' ad' + (totalAds !== 1 ? 's' : '') + ' configured');
                
                $('.post-type-thesiadm_ad .wp-header-end').after($stats);
            }
        }
        
        // Enhanced featured image selector
        if ($('.post-type-thesiadm_ad #postimagediv').length) {
            var $imageDiv = $('#postimagediv');
            var $adType = $('#thesiadm_ad_type');
            
            function updateFeaturedImageVisibility() {
                if ($adType.val() === 'image') {
                    $imageDiv.slideDown();
                    $imageDiv.find('.inside').prepend('<p class="description" style="background: #fff3e0; padding: 8px; border-radius: 4px; margin-bottom: 10px;">📸 Upload your ad image here (recommended: 728x90, 300x250, or 160x600)</p>');
                } else {
                    $imageDiv.slideUp();
                }
            }
            
            $adType.on('change', updateFeaturedImageVisibility);
            updateFeaturedImageVisibility();
        }
        
        // Keyboard shortcuts
        $(document).on('keydown', function(e) {
            // Ctrl/Cmd + S to save
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                $('#publish, #save-post').click();
            }
        });
        
        // Help tooltips
        $('.thesiadm-metabox th label').each(function() {
            var labelText = $(this).text();
            var helpTexts = {
                'Ad Position': 'Choose where this ad will appear on your site. You can create custom positions using shortcodes.',
                'Ad Type': 'Image ads use the Featured Image. Custom Code ads support Google AdSense, HTML, and JavaScript.',
                'Priority': 'When multiple ads match the same position, lower numbers show first (1 = highest priority).',
                'Target URLs': 'Enter the URLs where this ad should appear. Use relative paths like /blog/ or /products/.'
            };
            
            if (helpTexts[labelText]) {
                $(this).attr('title', helpTexts[labelText]);
                $(this).css('cursor', 'help');
            }
        });
        
    });
    
})(jQuery);
