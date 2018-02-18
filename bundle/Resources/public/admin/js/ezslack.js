document.addEventListener("DOMContentLoaded", function (event) {
    "use strict";
    var link = document.getElementById("novaezslack_shareonslack-tab");
    var icon = document.createElement("img");
    icon.setAttribute("src", "/bundles/novaezslack/admin/images/slack.svg");
    icon.setAttribute("height", "30");
    icon.setAttribute("width", "30");
    link.insertBefore(document.createElement("br"),link.firstChild);
    link.insertBefore(icon, link.firstChild);
    link.addEventListener("click", function (e) {
        var isDisabled = link.getAttribute('disabled') === 'disabled';
        if (isDisabled) {
            e.preventDefault();
            return;
        }
        var endpoint = link.getAttribute('href');
        var xhr = new XMLHttpRequest();
        xhr.open('GET', endpoint);
        xhr.onload = function () {
            if (xhr.status === 200) {
                link.setAttribute('disabled', 'disabled');
            }
        };
        xhr.send();
        e.preventDefault();
    });


});
