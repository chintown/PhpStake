function shareToFacebook_(title, summary, url, image) {
    var params = {
        'p[title]':title,
        'p[summary]':summary,
        'p[url]':url,
        'p[images][0]':image};
    params = $.map(params, function(v, k) {
        return (v) ? '&'+k+'='+urlEncodeIfNecessary(v) : v;
    });
    params = params.join('');
    var url = 'http://www.facebook.com/sharer.php?s=100'+params;
    window.open(url, 'sharer', 'toolbar=0,status=0,width=548,height=325');
}
function shareToFacebook(url) {
    var params = {
        'u':url
    };
    params = $.map(params, function(v, k) {
        return (v) ? k+'='+urlEncodeIfNecessary(v) : v;
    });
    params = params.join('&');
    var url = 'http://www.facebook.com/sharer.php?m2w&'+params;
    window.open(url, 'sharer', 'toolbar=0,status=0,width=548,height=325');
}
function shareToTwitter(content, url, tags) {
    var params = {
        'text':content,
        'url':url,
        'hashtags':tags.join(',')
    };
    params = $.map(params, function(v, k) {
        return (v) ? k+'='+urlEncodeIfNecessary(v) : v;
    });
    params = params.join('&');
    var url = 'http://twitter.com/share?'+params;
    window.open(url, 'sharer', 'toolbar=0,status=0,width=548,height=325');
}
function urlEncodeIfNecessary(raw) {
    raw = (raw) ? encodeURIComponent(raw) : '';
    return raw;
}