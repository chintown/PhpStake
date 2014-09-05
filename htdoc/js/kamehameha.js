var Goku = (function($) {
    function Goku(userOptions) {
        this.options = $.extend({
                debug: false
                ,indicator: $('<div></div>').attr('id', 'kamehameha-indicator').appendTo($('body'))
                ,watch: function (next) {
                    $(window).scroll(function() {
                        next();
                    });
                }
                ,shouldTrigger: function(event) {
                    var top = $(document).scrollTop();
                    var bottomLine = $(document).height() - $(window).height(); // <!DOCTYPE html> is a must to get correct result
                    return top >= bottomLine; // touch bottom?
                }
                ,onHold: function (counter, next) {
                    next();
                }
                ,onEmit: function (counter, next) {
                    next();
                }
                ,restingSeconds: 1
            }, userOptions);
        this.$indicator = this.options.indicator;
        this. isActing = false;
        this. counter = 0;
    }
    Goku.prototype.think = function () {
        if (!this.isActing && this.options.shouldTrigger()) {
            this.isActing = true;

            this.setStateClass('trigger');
            setTimeout(this.hold.bind(this), this.options.debug ? 1000 : 0);
        }
    };
    Goku.prototype.hold = function () {
        console.log('hold...');
        this.setStateClass('hold');

        this.counter += 1;
        this.options.onHold(this.counter, this.emit.bind(this));
    };
    Goku.prototype.emit = function () {
        console.log('emit!');
        this.setStateClass('emit');

        this.options.onEmit(this.counter, this.rest.bind(this));
    };
    Goku.prototype.rest = function () {
        console.log('take a rest...');
        this.setStateClass('rest');

        var _this = this;
        setTimeout(function () {
            _this.isActing = false;
            console.log('ready!');
            _this.setStateClass('ready');
        }, this.options.restingSeconds * 1000);
    };
    Goku.prototype.setStateClass = function(state) {
        this.$indicator.removeClass('rest trigger hold emit').addClass(state);
    };
    // -------------------------------------------------------------------------
    Goku.prototype.study = function () {
        this.options.watch(this.think.bind(this));
        this.setStateClass('ready');
    };
    return Goku;
})(jQuery);

