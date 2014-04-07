/*global document, jQuery, backstretch, $, backstretchImages*/
jQuery(document).ready(function($) {
    // if the expected data is not there return
    // if there are no images in the data return
    // if backstretch has not been loaded return
    if (backstretchImages === 'undefined' || backstretchImages.length === 0 || $.backstretch === 'undefined') {
        return;
    }
    // one image?
    if (backstretchImages.length === 1) {
        $.backstretch(backstretchImages[0]);
        return;
    }
    // 2 or more images?
    $.backstretch(backstretchImages, {
        duration: 3000,
        fade: 750
    });
});