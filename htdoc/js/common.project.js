
function ajaxMsgInfo(msg, seconds) {
    ajaxMsg(msg, 'alert-info', seconds);
}
function ajaxMsgSuccess(msg, seconds) {
    ajaxMsg(msg, 'alert-success', seconds);
}
function ajaxMsgWarning(msg, seconds) {
    ajaxMsg(msg, '', seconds);
}
function ajaxMsgError(msg, seconds) {
    ajaxMsg(msg, 'alert-error', seconds);
}
function ajaxMsg(msg, type, seconds) {
    seconds = parseInt(seconds, 10) || 3;
    seconds *= 1000;

    var $tar = $('.ajax-msg');
    $tar.removeClass('alert-success')
        .removeClass('alert-error')
        .removeClass('alert-info')
        ;
    $tar.addClass(type);
    $tar.text(msg).fadeIn();

    if (seconds > 0) {
        setTimeout(function() {
            $tar.fadeOut(1000);
        }, seconds);
    }
}