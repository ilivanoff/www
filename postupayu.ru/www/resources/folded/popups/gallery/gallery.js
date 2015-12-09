$(function() {
    PsJquery.onLoad($('.'+CONST.GALLERY_LIST), function($BOX) {
        $BOX.find('img.x-error').parent('li').remove();
    });
});