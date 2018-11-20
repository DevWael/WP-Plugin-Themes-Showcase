(function ($) {
    "use strict";
    /**
     * Show / Hide Demos box
     */
    (function () {
        var open_btn = $('.bb_open_showcase');
        open_btn.on('click', function (e) {
            e.preventDefault();
            $('.bb_theme_demos_container').addClass('visible transperancy');
            $('body').addClass('bb-ovh');
            $('.bb_theme_demos_floating_buttons').hide();
        });

        var close_button = $('.bb_theme_demos_close a');
        close_button.on('click', function (e) {
            e.preventDefault();
            $('.bb_theme_demos_container').removeClass('visible transperancy');
            $('body').removeClass('bb-ovh');
            $('.bb_theme_demos_floating_buttons').show();
        });
    })();
})(jQuery);
