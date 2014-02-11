function Delayer() {
    var settings = {
        cb: function () {}
    }
    ;lastPokeStamp = 0
    ;timers = []
    ;THRESHOLD_HALT = 1000 /* action quicker than this will cacel previous timers */
    ;DELAY_TIMER = 100 /* delay of between poke and actual trigger */
    ;

    // this.global;

    /* ======================================== */
    /* public configuration */
    this.init = function (sets) {
        $.extend(settings, sets);
    };
    this.poke = function () {
        var args = arguments;
        var currStamp = (new Date().getTime());

        timeDiff = currStamp - lastPokeStamp;
        timers.push(
            setTimeout(function() {trigger.apply(this, args);},
                        DELAY_TIMER)
        );
        if (timeDiff < THRESHOLD_HALT) {
          var count = timers.length-1;
          for (var k=0; k < count; k++) {
            clearTimeout(timers[0]);
            timers.shift(); /* pop head rather than tail */
          }
        }
        lastPokeStamp = currStamp;
    };
    this.getSettings = function () {
        return settings;
    };
    /* ======================================== */
    /* private flow */
    var trigger = function() {
        settings.cb.apply(this, arguments);
    };
};
