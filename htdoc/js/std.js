var De = {
    output: '',
    log: function() {},
    errlog: null,
    startTime: '',

    init: function(output) {
        De.output = output;
        if (typeof(output) == 'string') {
            if (output == 'firebug') {
                De.log = function() {
                    if (!window.DEV_MODE) return;
                    Function.prototype.apply.apply(console.log, [console, arguments]);
                }
            }
            else if (output == 'alert') {
                De.log = function() {
                    if (!window.DEV_MODE) return;
                    var s = '';
                    for(var i=0;i<arguments.length;i++) {
                        s+= ' '+arguments[i]+' ';
                    }
                    alert(s);
                }
            }
            else {
                var errlog;
                if (document.getElementById('errlog') != null) {
                    errlog = document.getElementById('errlog');
                }
                else {
                    errlog = document.createElement('div');
                    errlog.id = 'errlog';
                    errlog.style.zIndex = 999999;
                    errlog.style.color = 'red';
                    document.body.appendChild(errlog);
                }
                De.log = function () {
                    if (!window.DEV_MODE) return;
                    errlog.innerHTML += '<div>';
                    errlog.innerHTML += '<div><pre style="float:left">';
                    for(var i=0;i<arguments.length;i++) {
                        errlog.innerHTML += ' '+arguments[i]+' ';
                    }
                    errlog.innerHTML += '</pre></div>';
                    errlog.innerHTML += '<div><font color=blue>'+(new Date).getTime()+'</font></div>';
                    errlog.innerHTML += '</div>';
                }
            }
        }
        else {
            output();
        }
    },
    assert: function (condition, msg, para) {
        if (!condition) {
            console.error('ASSERTION FAILED: '+msg);
            if (defined(para)) {
                //De.listProp(para);
                De.log(para);
            }
            console.error('ASSERTION END');
            return false;
        }
        return true;
    },
    listProp: function (obj) {
        if (De.output == 'firebug') {
            for (var k in obj) {
                De.log("obj.",k," = ",obj[k]);
            }
        }
        else if (De.output == 'alert') {
            newline = '\n';
            var s='';
            for (var k in obj) {
                s+="obj."+i+" = "+obj[k]+newline;
                if (s.length > 300) {
                    De.log(s);
                    s = '';
                }
            }
            De.log(s);
        }
        else {
            newline = '<br/>';
            var s='';
            for (var k in obj) {
                s+="obj."+i+" = "+obj[k]+newline;
            }
            De.log(s);
        }
    },
    time: function (){
        if (!window.DEV_MODE) return;
        var isReset = false;
        var extra = [];
        if (arguments.length > 0){
            for (var i=0; i < arguments.length; i++) {
                if (arguments[i] === true) {
                    isReset = true;
                } else {
                    extra.push(arguments[i]);
                }
            }
        }
        if (isReset) {
            De.startTime = (new Date).getTime();
        }
        var delta = ((new Date).getTime()) - De.startTime;
        delta = delta / 1000;
        var caller = arguments.callee.caller.name;
        caller = (caller) ? caller : '(func)';
        // var caller = '';
        var msg = delta+" "+caller+".";
        if (extra.length !== 0) {
            De.log(msg, extra);
        } else {
            De.log(msg);
        }

    },

    dummy: 0
}

var ie = (function(){
    var undef,
        v = 3,
        div = document.createElement('div'),
        all = div.getElementsByTagName('i');
    while (
        div.innerHTML = '<!--[if gt IE ' + (++v) + ']><i></i><![endif]-->',
            all[0]
        );
    return v > 4 ? v : undef;
}());

if (ie) {
    De.init('ie');
} else {
    De.init('firebug');
}
var de = De; //alias

function defined(v) {
    var f = 1;
    if (typeof (v) === 'undefined') {
        f = 0;
    } else if (typeof (v) === 'number') {
        f = 1
    } else if (typeof (v) === 'string') {
        f = 1
    } else if (typeof (v) === 'function') {
        f = 1
    } else if (!v) {
        f = 0;
    }
    return f;
}

function getKCode(event) {
  return event.which || event.keyCode
}

// implement JSON.stringify serialization
var JSON = JSON || {};
JSON.stringify = JSON.stringify || function (obj) {
    var t = typeof (obj);
    if (t != "object" || obj === null) {
        // simple data type
        if (t == "string") obj = '"'+obj+'"';
        return String(obj);
    }
    else {
        // recurse array or object
        var n, v, json = [], arr = (obj && obj.constructor == Array);
        for (n in obj) {
            v = obj[n]; t = typeof(v);
            if (t == "string") v = '"'+v+'"';
            else if (t == "object" && v !== null) v = JSON.stringify(v);
            json.push((arr ? "" : '"' + n + '":') + String(v));
        }
        return (arr ? "[" : "{") + String(json) + (arr ? "]" : "}");
    }
};

// FUTURE maybe separated these as one file

// http://stackoverflow.com/questions/4877326/how-can-i-tell-if-a-string-contains-multibyte-characters-in-javascript
// use: None
function containsSurrogatePair(str) {
    return /[\uD800-\uDFFF]/.test(str);
}
// http://stackoverflow.com/questions/11200451/extract-substring-by-utf-8-byte-positions
// use: std.js
function encode_utf8(str) {
  return unescape(encodeURIComponent(str));
}
// use: std.js, [Manager.js]
function length_utf8_length(str) {
    // by chintown
    return encode_utf8(str).length;
}
// use: std.js, [Manager.js]
function substr_utf8_bytes(str, startInBytes, lengthInBytes) {

   /* this function scans a multibyte string and returns a substring.
    * arguments are start position and length, both defined in bytes.
    *
    * this is tricky, because javascript only allows character level
    * and not byte level access on strings. Also, all strings are stored
    * in utf-16 internally - so we need to convert characters to utf-8
    * to detect their length in utf-8 encoding.
    *
    * the startInBytes and lengthInBytes parameters are based on byte
    * positions in a utf-8 encoded string.
    * in utf-8, for example:
    *       "a" is 1 byte,
            "ü" is 2 byte,
       and  "你" is 3 byte.
    *
    * NOTE:
    * according to ECMAScript 262 all strings are stored as a sequence
    * of 16-bit characters. so we need a encode_utf8() function to safely
    * detect the length our character would have in a utf8 representation.
    *
    * http://www.ecma-international.org/publications/files/ecma-st/ECMA-262.pdf
    * see "4.3.16 String Value":
    * > Although each value usually represents a single 16-bit unit of
    * > UTF-16 text, the language does not place any restrictions or
    * > requirements on the values except that they be 16-bit unsigned
    * > integers.
    */

    var resultStr = '';
    var startInChars = 0;

    // scan string forward to find index of first character
    // (convert start position in byte to start position in characters)

    for (bytePos = 0; bytePos < startInBytes; startInChars++) {

        // get numeric code of character (is >128 for multibyte character)
        // and increase "bytePos" for each byte of the character sequence

        ch = str.charCodeAt(startInChars);
        bytePos += (ch < 128) ? 1 : encode_utf8(str[startInChars]).length;
    }

    // now that we have the position of the starting character,
    // we can built the resulting substring

    // as we don't know the end position in chars yet, we start with a mix of
    // chars and bytes. we decrease "end" by the byte count of each selected
    // character to end up in the right position
    end = startInChars + lengthInBytes - 1;

    for (n = startInChars; startInChars <= end; n++) {
        // get numeric code of character (is >128 for multibyte character)
        // and decrease "end" for each byte of the character sequence
        ch = str.charCodeAt(n);
        end -= (ch < 128) ? 1 : encode_utf8(str[n]).length;

        resultStr += str[n];
    }

    return resultStr;
}

var RE_NEWLINE_WINDOWS = new RegExp("\r\n", 'g');
var RE_NEWLINE_MAC = new RegExp("\r", 'g');
function normalizeNewline (text) {
    // http://darklaunch.com/2009/05/06/php-normalize-newlines-line-endings-crlf-cr-lf-unix-windows-mac
    text = text.replace(RE_NEWLINE_WINDOWS, "\n");
    text = text.replace(RE_NEWLINE_MAC, "\n");
    return text;
}
// (space) ~!@#$%^&*=\;'/()[]{}<>`+|:"?
var RE_INVALID_FILE_CHARS = new RegExp('[ ~!@#$%\^&*=\\\\;\'\/(){}<>\\[\\]`+|:"]', 'g');
function normalizeFilename (text, prefixPreventEmpty) {
    prefixPreventEmpty = prefixPreventEmpty || 'fn_'+(new Date()).getTime();
    text = text.replace(RE_INVALID_FILE_CHARS, "_");
    if (text.replace(/_/g, '') === '') {
        text = prefixPreventEmpty + text;
    }
    return text;
}
//de.log(normalizeFilename('0~1!2@3#4$5%6^7&8*9=0'));
//de.log(normalizeFilename('a\\b;c\'d/e(f)g[h]i{j}k<l>m`n+o|p:q"rstuvwxyx'));
//de.log(normalizeFilename('!@#$@#%#^#$%^'));
//de.log(normalizeFilename('5/5/5'));


function setCookie(key, value, days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days*24*60*60*1000));
        expires = "; expires=" + date.toGMTString();
    }
    document.cookie = key + "=" + value +expires + "; path=/";
}
function getCookie(key) {
    var cookieStr = document.cookie;
    cookieStr = (cookieStr) ? cookieStr+";" : cookieStr; // make the pattern consistent
    var matchResult = cookieStr.match(key+'=(.*?);');
    if (matchResult) {
        return matchResult[1];
    } else {
        return null;
    }
}
function removeCookie(key) {
    setCookie(key, "", -1);
}