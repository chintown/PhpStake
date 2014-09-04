function Goku() {
    var isActing = false
        , kamehameha
        , counter = 0
        ;
    /* ====================================================================== */
    this.prepare = function() {
        $(window).scroll(function(e) {
            if (!isActing && isRightPos()) {
                emit();
            }
        });
    };
    this.setKamehameha = function(userKamehameha) {
        kamehameha = userKamehameha;
    };
    /* ====================================================================== */
    var isRightPos = function () {
        var top = $(this).scrollTop();
        var bottomLine = $(document).height() - $(window).height();
        var isAtBottom = top >= bottomLine;
        return isAtBottom;
    };
    var emit = function () {
        isActing = true;
        counter += 1;
        console.log('set #'+counter+' kamehameha...');
        kamehameha(counter, takeRest);
    };
    var defaultKamehameha = function (counter, next) {
        console.log('boooooooooooooooooom!!!');
        next();
    };
    var takeRest = function() {
        isActing = false;
    };

    kamehameha = defaultKamehameha;
}