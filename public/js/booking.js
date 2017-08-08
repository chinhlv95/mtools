(function($) {

    var adminFn = window.adminFn || {};

    $.extend(adminFn, {

        version : 0.1,
        context : null,

        init : function() {
            $("form").first().find('input[type=text]').first().focus();
            $(".main-menu li a").each(function() {
                var current = $(this);
                var parent = current.parent();
                parent.removeClass("active");
                var parent1 = parent.parent().parent();
                // parent1.removeClass("active");

                if (baseUrl + location.pathname == current.attr("href")) {
                    parent.addClass("active");
                    parent1.addClass("active");
                    return true;
                }
            });
        }
    });

    window.adminFn = adminFn;

})(jQuery);

$(document).ready(function() {
    adminFn.init();
});