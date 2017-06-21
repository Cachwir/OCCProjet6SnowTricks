/**
 * Created by cachwir on 06/12/16.
 */
(function() {

    "use strict";

    var $flashLoader;
    var isSendingRequest = false;

    // flash messages
    function displayFlash(type, message, controlled_display, additionnal_classes) {

        var flashElement = '<div class="alert alert-'+ type + ' ' + additionnal_classes +' absolute_flash">' +
            '<button class="close" data-close="alert"></button>' +
            '<span class="flash_content">'+ message +'</span>' +
            '</div>';

        var $flashElement = $(flashElement);

        $flashElement.addClass("alert-" + type);

        $('.flash_messages').prepend($flashElement);

        if (!controlled_display) {
            $flashElement.fadeIn(500, function() {
                setTimeout((function() {
                    $(this).fadeOut(1000, function() {
                        $(this).remove();
                    }).bind(this);
                }).bind(this), 5000);
            });
        }

        return $flashElement;
    }

    function displayFlashLoader() {
        if (typeof $flashLoader == "undefined")
        {
            $flashLoader = displayFlash("info", "<img src='/path/to/loader/loader.gif' alt='loading...' class='loader'>", true, 'darkened');
        }

        $flashLoader.stop(true).fadeIn(500);
    }

    function hideFlashLoader() {
        if (typeof $flashLoader != "undefined")
        {
            $flashLoader.stop(true).fadeOut(500, function() {
                $flashLoader.remove();
                $flashLoader = undefined;
            });
        }
    }

    function notifySendingRequest()
    {
        displayFlash("warning", "Une action est déjà en cours de réalisation...");
    }

    window.displayFlash = displayFlash;
    window.displayFlashLoader = displayFlashLoader;
    window.hideFlashLoader = hideFlashLoader;
    window.isSendingRequest = isSendingRequest;
    window.notifySendingRequest = notifySendingRequest;
})();