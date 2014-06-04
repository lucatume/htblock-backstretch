/*global document, jQuery, backstretch, $, backstretchData*/
jQuery(document).ready(function($) {
    if (!$.backstretch) {
        return;
    }
    if (backstretchData === 'undefined') {
        return;
    }
    if (backstretchData['imageSources'] === 'undefined' || backstretchData['imageSources'].length === 0 || $.backstretch === 'undefined') {
        return;
    }
    var srcs = backstretchData['imageSources'],
        params = {
            duration: parseInt(backstretchData['duration'], 10) || 3000,
            fade: parseInt(backstretchData['fade'], 10) || 500
        };
    // one image?
    if (srcs.length === 1) {
        $.backstretch(srcs[0], params);
        return;
    }
    // 2 or more images?
    $.backstretch(srcs, params);
});