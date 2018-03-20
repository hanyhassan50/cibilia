(function (root, factory) {
    // check to see if 'knockout' AMD module is specified if using requirejs
    if (typeof define === 'function' && define.amd &&
        typeof require === 'function' && typeof require.specified === 'function') {

        // AMD. Register as an anonymous module.
        define(['jquery'], factory);
    } else {
        // Browser globals
        factory(root.jQuery);
    }
})(this, function ($) {

    (function ($) {

        $.fn.animateIcon = function (options) {

            if (this.length == 1) {
                var _id = this[0].id;
            }

            if (typeof Pathformer === "function" && typeof Vivus === "function") {

                var settings = $.extend({
                    type: 'oneByOne',
                    start: 'inViewport',
                    dashGap: 20,
                    duration: 140
                }, options);

                $('#' + _id + ' svg').each(function () {
                    var iconID = $(this).attr('id');
                    var iconVar = iconID.replace('-', '');

                    window['tc' + iconVar] = new Vivus(iconID, settings);

                });

                $('#' + _id).each(function () {
                    $("#" + $(this).attr('id')).delegate(".ai-icon", "mouseenter", function () {
                        var iconID = $(this).find('svg').attr('id');
                        if (!iconID) return false;
                        var iconVar = iconID.replace('-', '');
                        window['tc' + iconVar].reset().play();
                    });
                });

            } else {

                
                console.info("To use animateIcon make sure dependent plugins are available");

            }

            return this;
        };

    }(jQuery));


});