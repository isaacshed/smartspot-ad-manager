(function ($) {
    'use strict';

    function syncVisibility($el) {
        var $wrap = $el.closest('.widget, .widget-inside, .control-section, .widget-control-actions').closest('.widget, .widget-inside, .control-section');
        var mode = $el.val();
        if ('single' === mode) {
            $wrap.find('.thesiadm-widget-position-field').hide();
            $wrap.find('.thesiadm-widget-ad-field').show();
        } else {
            $wrap.find('.thesiadm-widget-position-field').show();
            $wrap.find('.thesiadm-widget-ad-field').hide();
        }
    }

    $(document).on('change', '.thesiadm-widget-mode', function () {
        syncVisibility($(this));
    });

    $(document).ready(function () {
        // Initialize visibility for any pre-existing widget forms (widgets screen,
        // customizer, and after an AJAX widget-save re-render).
        $('.thesiadm-widget-mode').each(function () {
            syncVisibility($(this));
        });

        // Listen for widget-updated events fired by WordPress after saving a
        // widget so the toggle state stays correct after a server-side re-render.
        $(document).on('widget-updated widget-added', function () {
            $('.thesiadm-widget-mode').each(function () {
                syncVisibility($(this));
            });
        });
    });
})(jQuery);
