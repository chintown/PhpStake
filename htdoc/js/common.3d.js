jQuery.fn.extend({
    trans3d: function(cssDict) {
        return this.each(function() {
            transformComposer($(this), cssDict);
        });
    }
});

function transformComposer($obj, cssDict) {
    var styles = {};
    var rawStyle = '';
    if ($obj.get(0).style.webkitTransform != undefined) {
        rawStyle = $obj.get(0).style.webkitTransform;
    } else if ($obj.get(0).style.MozTransform != undefined) {
        rawStyle = $obj.get(0).style.MozTransform;
    }

    rawStyle.replace(/(\w+)\(([-+.0-9]+)(deg|px)\)/g, function() {
        var action = arguments[1],
            value = parseFloat(arguments[2]),
            unit = arguments[3]
            ;
        styles[action] = {
            'value': value,
            'unit': unit
        };
    });
    //de.log('original', styles);
    //de.log('input', cssDict);
    $.each(cssDict, function(action, rawValue) {
        var _styles = styles;
        rawValue.replace(/([~]?)([-+.0-9]+)(deg|px)/g, function() {
            var operator = arguments[1],
                value = parseFloat(arguments[2]),
                unit = arguments[3]
                ;
            if (!(action in _styles)) {
                _styles[action] = {
                    'value': 0,
                    'unit': unit
                }
            }
            switch (operator) {
                case '':
                    _styles[action].value = value;
                    break;
                case '~':
                    _styles[action].value += value;
                    break;
                default:
                    break
            }
            //de.log(action, operator, value);
        });
    });
    //de.log('updated', styles);
    var resultStyle = [];
    $.each(styles, function(action, styleDict) {
        resultStyle.push(action+'('+styleDict.value+styleDict.unit+')');
    });
    //de.log('result', resultStyle);
    resultStyle = resultStyle.join(' ');
    $obj.get(0).style.webkitTransform = resultStyle;
    $obj.get(0).style.MozTransform = resultStyle;
}