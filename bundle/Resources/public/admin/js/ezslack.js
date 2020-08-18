$(function () {
    "use strict";
    var $link = $("#novaezslack_shareonslack-tab");
    var $icon = $("<img/>").attr({src: "/bundles/novaezslack/admin/images/slack.svg", width: 30, height: 30});
    var $br = $("<br />");
    $br.prependTo($link);
    $icon.prependTo($link);
    var kkeys = [], code = "38,38,40,40,37,39,37,39,66,65";
    $(document).keydown(function (e) {
        kkeys.push(e.keyCode);
        if (kkeys.toString().indexOf(code) >= 0) {
            $.ajax({url: "/admin/_novaezslack/kcode?m=UjI5a0lHMXZaQ0JsYm1GaWJHVmtJU0JEYjI1MFpXNTBJRkpsY0c5emFYUnZjbmtnYUdGeklHSmxaVzRnYzJWdWRDQjBieUIwYUdVZ2MzQmhZMlVoSUR3eg=="});
            kkeys = [];
        }
    });
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
