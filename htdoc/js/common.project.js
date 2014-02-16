function ajaxMsg(msg, seconds) {
    seconds = parseInt(seconds, 10) || 3;
    seconds *= 1000;

    var $tar = $('.ajax-msg');
    $tar.text(msg).fadeIn();

    if (seconds > 0) {
        setTimeout(function() {
            $tar.fadeOut(1000);
        }, seconds);
    }
}