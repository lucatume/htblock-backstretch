/*! Backstretch block - v0.1.0
 * http://theaveragedev.com
 * Copyright (c) 2014; * Licensed GPLv2+ */
jQuery(document).ready(function($) {
    // if the expected data is not there return
    // if there are no images in the data return
    // if backstretch has not been loaded return
    if (backstretchData['imageSources'] === 'undefined' || backstretchData['imageSources'].length === 0 || $.backstretch === 'undefined') {
        return;
    }
    var srcs = backstretchData['imageSources'],
        params = {
            duration: parseInt(backstretchData['duration'], 10),
            fade: parseInt(backstretchData['fade'], 10)
        };
    // one image?
    if (srcs.length === 1) {
        $.backstretch(srcs[0], params);
        return;
    }
    // 2 or more images?
    $.backstretch(srcs, params);
});