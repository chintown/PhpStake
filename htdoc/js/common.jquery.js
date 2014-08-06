
// http://stackoverflow.com/questions/3404508/cross-browsers-mult-lines-text-overflow-with-ellipsis-appended-within-a-widthhe
// target must have fixed width and height
function truncate(target, w, h) {
    target = ($.type(target) === 'string')
                ? $(target)
                : target;
    var inner = target.find('.truncation');
    if (inner.length === 0) {
        // inner = $('<span>').addClass('truncation').text(target.text());
        // target.append(inner);
        console.error('mark "truncation" class in the target of ',target);
        return target.text();
    }

    var expH = $('<div>').height(h).height(); //target.height();
    var count = 0, changed = false;
    while(inner.outerHeight() > expH) {
        inner.text(function(i, text) {
            changed = true;
            return text.replace(/\W*\s(\S)*$/, '...');
        });
        count += 1;
        if (count > 200) {
            console.error('truncation can not fulfill  ', h, 'or', expH);
            break;
        }
    }
    if (changed) {
        target.width(w);
        target.height(h);
    }
    return target.text();
}

// try to fix jquery's replaceWith
function replaceDom($replacement, $origin) {
    $replacement.hide();
    $replacement.insertBefore($origin);
    $origin.remove();
    $replacement.show();
}

function completeLinkState(url) {
    /*
     hash: "#plain-shelf"
     host: "localhost"
     hostname: "localhost"
     href: "http://localhost/editor/list.php?q={%22department%22:%22%E5%A5%97%E6%9B%B8%22}#plain-shelf"
     origin: "http://localhost"
     pathname: "/editor/list.php"
     port: ""
     protocol: "http:"
     search: "?q={%22department%22:%22%E5%A5%97%E6%9B%B8%22}"
    */
    var loc = window.location;
    return url + loc.hash;
}

function getLinkParam(key) {
    var info = window.location;
    var params = parseQueryString(info.search);
    return params[key];
}

function updateLinkParams(updates) {
    var info = window.location;
    var params = parseQueryString(info.search.substr(1));

    $.each(updates, function (key, val) {
        params[key] = encodeURI(val);
    });

    var query = [];
    $.each(params, function (k, v) {
        query.push(k + "=" + v);
    });
    return info.origin + info.pathname + '?' + query.join('&') + info.hash;
}

function parseQueryString(query) {
    var params = {}, queries, pair, i, l;
    if (query === '') {
        return params;
    }

    // Split into key/value pairs
    queries = query.replace('?', '').split("&");

    // Convert the array of strings into an object
    for ( i = 0, l = queries.length; i < l; i++ ) {
        pair = queries[i].split('=');
        params[pair[0]] = pair[1];
    }

    return params;
}

function getClassByPattern (ptn) {
    // alternative: (css.match (/\blv\d+/g) || []).join(' ')
    return (function (index, css) {
        return ((new RegExp(ptn, 'g')).exec(css) || []).join(' ');
    });
}

function toggleRadio(_this) {
    var $this = $(_this);
    $this.parent().find('input[type="radio"]').prop('checked', true);
    $this.prop('checked', true);
}

function autoHideSoftKeyboard() {
    // http://stackoverflow.com/questions/2890216/hide-keyboard-in-iphone-safari-webapp
    $(document).on('touchstart', function (e) {
        var $target = $(e.target);
        if (!$target.is('input') && !$target.is('textarea')) {
            document.activeElement.blur();
        }
    });
}
// -----------------------------------------------------------------------------

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

function initDropHint(targets, className) {
    var $body = $('body');
    var dragOverHandler = function (e) {
        targets.forEach(function($target, idx) {
            $target.addClass(className);
        });
    };
    var dragLeaveHandler = function (e) {
        targets.forEach(function($target, idx) {
            $target.removeClass(className);
        });
    };
    targets.forEach(function($target, idx) {
        var target = $target.get(0);
        $body.bind("dragover", dragOverHandler);
        $body.bind("dragleave", dragLeaveHandler);
        target.addEventListener('dragenter', function (e) {
            $('body').unbind("dragleave");
        }, true);
        target.addEventListener('dragleave', function (e) {
            $('body').bind("dragleave", dragLeaveHandler);
        }, true);
        target.addEventListener('drop', function (e) {
            $(target).removeClass(className);
        });
    });
}

function setClassTogglingWhileMouseOver($hoverAtDom, toggleAtTarget, className) {
    setClassTogglingWhileHovering($hoverAtDom, toggleAtTarget, className, false);
}
function setClassTogglingWhileMouseOut($hoverAtDom, toggleAtTarget, className) {
    setClassTogglingWhileHovering($hoverAtDom, toggleAtTarget, className, true);
}
function setClassTogglingWhileHovering($hoverAtDom, toggleAtTarget, className, isReversedToggling) {
    $hoverAtDom.live('mouseover', function(e) {
        var $target = _getToggleAtDom(e.target, toggleAtTarget);
        if (isReversedToggling) {
            $target.removeClass(className);
        } else {
            $target.addClass(className);
        }
    });
    $hoverAtDom.live('mouseout', function(e) {
        var $target = _getToggleAtDom(e.target,     toggleAtTarget);
        if (isReversedToggling) {
            $target.addClass(className);
        } else {
            $target.removeClass(className);
        }
    });
}
function _getToggleAtDom(hoverAtDom, toggleAtTarget) {
    var $target;
    if (toggleAtTarget === null) {
        $target = $(hoverAtDom);
    } else if (typeof toggleAtTarget == 'string') {
        $target = $(hoverAtDom).find(toggleAtTarget);
    } else {
        $target = toggleAtTarget;
    }
    return $target;
}