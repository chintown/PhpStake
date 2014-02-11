// "use strict";

// -----
function bindEvents() {
    de.time();
}
function init() {
    bindEvents();
    de.time('INITIALIZATION DONE');
}
$(document).ready(function() {
    init();
});
