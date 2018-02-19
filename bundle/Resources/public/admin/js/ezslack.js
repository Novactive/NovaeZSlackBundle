$(function () {
    "use strict";
    var $link = $("#novaezslack_shareonslack-tab");
    var $icon = $("<img/>").attr({src: "/bundles/novaezslack/admin/images/slack.svg", width: 30, height: 30});
    var $br = $("<br />");
    $br.prependTo($link);
    $icon.prependTo($link);
    $link.on('click', function () {
        if ($(this).attr('disabled') !== 'disabled') {
            $.ajax({
                url: $link.attr('href'),
                success: function () {
                    $link.attr('disabled', 'disabled');
                }
            });
        }
        return false;
    });
});
