
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
    queries = query.split("&");

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