/*
    for getting right switching effect,
    set hide class in your css file
*/
function Flipper(jQuerySelector) {
    var tar = $(jQuerySelector);
    var records = [];
    var idxList = [];
    var oajax = [];
    var panel;
    var controller;
    var controllerForward;
    var controllerBackward;

    var INITIAL = 0;
    var FORWARD = 1;
    var BACKWARD = -1;
    var numDisplay = 1;
    var isLoop = 1;
    var eidx= 0;
    var sidx= 0;
    var lastSidx= 0;

    var backwardDead = false; // default false for LOOP
    var forwardDead = false;

    var movingDirection = 0;

    var LOOP = 1;
    var NOLOOP = 0;
    var INFINITY = 2;
    this.LOOP = 1;
    this.NOLOOP = 0;
    this.INFINITY = 2;

    /* ======================================== */
    /* public configuration */
    this.init = function () {
        /* register a select */
        panel = $('<div>');
        var d = new Date();
        var ds = ""+d.getDate()+d.getHours()+d.getMinutes()+d.getSeconds()+d.getMilliseconds();
        var stamp = ('' == tar.attr('id'))?(ds):(tar.attr('id'));
        panel.attr('id', 'flipper-canvas-'+stamp).addClass('flipper-canvas');

        // single \u2039, \u203a"
        // double \253, \273
        var o1 = $('<input>').attr('type','button').attr('value',"\u2039").click(function(e){moveBackward();e.stopPropagation()});
        controllerBackward = $('<span>').attr('id','flipper-backward-'+stamp).addClass('flipper-backward').addClass('flipper-ward').append(o1);
        controllerBackward.click(function(e){moveBackward();e.stopPropagation()});
        var o2 = $('<input>').attr('type','button').attr('value',"\u203a").click(function(e){moveForward();e.stopPropagation()});
        controllerForward = $('<span>').attr('id','flipper-forward-'+stamp).addClass('flipper-forward').addClass('flipper-ward').append(o2);
        controllerForward.click(function(e){moveForward();e.stopPropagation()});
        controller = $('<div>').attr('id','flipper-controller-'+stamp).addClass('flipper-controller').append(controllerBackward).append(controllerForward);
    };
    this.setNumDisplay = function(num) {
        numDisplay = num;
    };
    this.getRecords = function() {
        return records;
    };
    this.setRecordsByList = function(recordList) {
        records = recordList;
        backend = backendDirect;
    };
    this.setRecordsByAjax = function(ajaxParameter) {
        /* ajaxParameter: { action: 'http://',
            para: {...}, // remain an element "q" as user input query
            callback: function(o){list=do_something(o); return list;} }
         */
        oajax = ajaxParameter;
        if ('undefined' == typeof(oajax.para)) {
            oajax.para = {};
        }
        backend = backendAjax;
    };
    this.setLoop = function(isLoopEnable) {
        isLoop = isLoopEnable;
    };
    this.setExistingCheck = function(isExistingCheck) {
        if (isExistingCheck) {
            renderRecords = renderRecordsIfInexisted;
        }
    };
    this.setRecordRender = function(cb) {
        renderRecord = cb;
    };
    this.getRecordRender = function(cb) {
        return renderRecord;
    };
    this.setRecordRenders = function(cb) {
        renderRecords = cb;
    };
    this.setDeadendHandler = function(cb) {
        deadendHandler = cb;
    };
    this.renderLayout = function() {
        renderControlers();
        tar.append(panel);

        move(INITIAL);
    };
    this.getPanelId = function() {
        return panel.attr('id');
    };
    this.getSIdx = function() {
        return sidx;
    };
    this.setSIdx = function(si) {
        sidx = si;
    };
    this.reset = function(idx) {
        sidx = idx || 0;
        move(INITIAL);
    };
    this.isBackwardDead = function() {
        return _isBackwardDead();
    };
    this.isForwardDead = function() {
        return _isForwardDead();
    };
    this.getCurrRecord = function() {
        return records[sidx];
    };
    this.getLastRecord = function() {
        return records[lastSidx];
    };
    this.getMovingDirection = function() {
        return movingDirection;
    };
    /* ======================================== */
    /* private flow */
    var _isBackwardDead = function() {
        return (sidx <= 0);
    };
    var _isForwardDead = function() {
        return (records.length === 0 || (sidx+numDisplay) === records.length);
    };
    var moveForward = function() {
        movingDirection = FORWARD;
        move(FORWARD);
    };
    var moveBackward = function () {
        movingDirection = BACKWARD;
        move(BACKWARD);
    };
    var move = function(direction) {
        lastSidx = sidx;
        if (    (direction === FORWARD && _isForwardDead()) ||
                (direction === BACKWARD && _isBackwardDead())   ) {
                deadendHandler();
                return;
        }

        /* goal: calculated the sequence of item idex
         * (if numDisplay = 3)
         * ...-4 -3 -2 -1 0 1 2  3 4 ...     //idx will be accumulated without boundary
         *                .  >|              // initial sidx
         *                       .  >|       // after moving forwarding
         *        .    >|                    // after moving backward
         */
        var maxIdx = records.length-1;
        var num = records.length;
        idxList = [];
        var pseudo_sidx = sidx+numDisplay*direction;
        var pseudo_eidx;
        if (isLoop == NOLOOP) {
            // sidx
            if (pseudo_sidx < 0) sidx = 0;
            else if (maxIdx < pseudo_sidx) sidx = sidx;
            else sidx = pseudo_sidx;

            // eidx
            pseudo_eidx = sidx+numDisplay-1;
            eidx = (pseudo_eidx > maxIdx)?(maxIdx):(pseudo_eidx);

            forwardDead = (eidx === maxIdx);
            backwardDead = (sidx === 0);

            // fill the idxList
            if (sidx !== -1) {
                for (i=sidx; i<=eidx; i++) { idxList.push(i); }
            }
        }
        else if (isLoop == INFINITY) {
            sidx = pseudo_sidx;
            eidx = sidx+numDisplay-1;
        }
        else {
            // sidx
            if (pseudo_sidx < 0) {
                sidx = (num)-(pseudo_sidx*-1);
            }
            else if (maxIdx < pseudo_sidx) {
                sidx = pseudo_sidx % num;
            }
            else sidx = pseudo_sidx;

            // fill the idxList
            for (var count=0; count<numDisplay; count++) {
                var i = sidx + count;
                i = (i > maxIdx)?(i % num):(i);
                idxList.push(i);
            }
        }
        backend(idxList);
    };
    var backend;
    var backendDirect = function(idxList) {
        var list = [];
        $.each(idxList, function(i, v) {
            list.push(records[v]);
        });
        frontend(list);
    };
    var backendAjax = function(idxList) {
        oajax['para']['sidx'] = sidx;
        oajax['para']['eidx'] = eidx;
        $.get(oajax['action'], oajax['para'], function(o) {
            var list;
            if ('undefined' != typeof(oajax['callback'])) {
                list = oajax['callback'](o);
            } else {
                list = o;
            }
            frontend(list);
        });
    };
    var frontend = function (list) {
        renderRecords(list);
        //renderControlers();
    };
    var renderRecords = function (list) {
        panel.html('');
        $.each(list, function(i, v) {
            var o = renderRecord(i, v);
            panel.append(o);
        });
    };

    var getIdxByItem = function(item) {
        return $.inArray(item, records);
    };
    var renderRecordsIfInexisted = function (list) {
        panel.children().hide().css('visibility', 'hidden').addClass('hide');

        $.each(list, function(i, v) {
            var idx = getIdxByItem(v);
            var tar = panel.find('[data-flip-idx='+idx+']');
            if (tar.length !== 0) {
                tar.show().css('visibility', 'visible').removeClass('hide');
                return;
            }
            var o = renderRecord(idx, v);
            o.attr('data-flip-idx', idx);
            panel.append(o);
            // TODO considering insertion order
        });
    };
    var renderRecord = function (itemIdx, item) {
        /* default method */
        return $('<div>').attr('id', panel.attr('id') + "-" + itemIdx).html(item);
    };
    var renderControlers = function() {
        // tar.append(controllerBackward);
        // tar.append(controllerForward);
        tar.append(controller);
    };
    var deadendHandler = function() {
    };

    /* ======================================== */
    /* private tool */

};
