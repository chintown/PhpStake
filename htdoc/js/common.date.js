// http://stackoverflow.com/questions/1353684/detecting-an-invalid-date-date-instance-in-javascript
function isValidDate(d) {
    if ( Object.prototype.toString.call(d) !== "[object Date]" )
        return false;
    return !isNaN(d.getTime());
}
function pad(n){
    return n < 10 ? '0' + n : n;
}
function unixToDate(unix) {
    if (unix === null) {
        return false;
    }
    var d = new Date(unix * 1000);
    if (!isValidDate(d)) {
        de.log('[Error] invalid input for initiate date, ' + unix);
        return false;
    }
    return d;
}
function dateToUnix(d) {
    return d.getTime() / 1000;
}

function isoToDate(iso) {
    // var iso = "2010-11-30T08:32:22+0000";
    // Replace non-digit characters with a space
    iso = iso.replace(/\D/g," ");
    // Split the string on space
    var date_parts = iso.split(" ");

    // subtract 1 from month to use in Date constructor
    var yyyy = date_parts[0] * 1,
        mm = date_parts[1] - 1,
        dd = date_parts[2] * 1,
        hh = date_parts[3] * 1,
        mi = date_parts[4] * 1;

    // Now, the date_parts has year, month, date and so on
    return new Date(yyyy, mm, dd, hh, mi);
}

// http://stackoverflow.com/questions/2573521/how-do-i-output-an-iso-8601-formatted-string-in-javascript
function dateToIso(d) {
    return d.getFullYear() + '-'
        + pad(d.getMonth() + 1) + '-'
        + pad(d.getDate()) + 'T'
        + pad(d.getHours()) + ':'
        + pad(d.getMinutes()) + ':'
        + pad(d.getSeconds());
}