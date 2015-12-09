$(function() {
    /*
     * Регистрируется процессор
     */
    PsIdentPagesManager.register({
        sitemap: {
            load: function(onLoadDone) {
                onLoadDone(PsNavigation.map());
            },
            onAfterShow: function(page) {
                //После показа карты
                var $map = page.div.find('ul.sitemap');
                $map.find('.current').removeClass('current').children('a.uAreHere').remove();
        
                function getMapHr() {
                    var url = defs.url;
                    var hash = window.location.hash ? window.location.hash.ensureStartsWith('#') : '';
                    var $a = $map.find('a[href="' + url + hash + '"]');
                    if(!$a.isEmptySet()) return $a;
                    if(!hash) return null;
                    $a = $map.find('a[href="' + url + '"]');
                    return $a.isEmptySet() ? null : $a;
                }
        
                var $mapHr = getMapHr();
                if(!$mapHr) return;// Не удалось определить ссылку на карте для данной страницы
        
                var $uah = crA($mapHr.attr('href')).addClass('uAreHere').html('Вы здесь');
        
                $mapHr.parent('li').addClass('current').append($uah);
        
                $.scrollTo($mapHr, 700, {
                    over:-25
                });
            }
        }
    });
});