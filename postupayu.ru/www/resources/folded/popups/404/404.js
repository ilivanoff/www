/*
 * Перезагрузим страницу на логин/логаут
 */
$(function() {
    var onLoginLogout = function(){
        locationReload();
    }
    
    PsLocalBus.connect(PsLocalBus.EVENT.LOGIN, onLoginLogout);
    PsLocalBus.connect(PsLocalBus.EVENT.LOGOUT, onLoginLogout);
})


/*
 * Добавление постов в избранное
 */

$(function() {
    var $stars = $('.popup-tool img.star');
    if ($stars.size()==0) return;//---
    
    function getBox(ImgOrBox) {
        return $(ImgOrBox).extractParent('.popup-tool');
    }
    
    function getImg(ImgOrBox) {
        return $(ImgOrBox).extractTarget('img.star');
    }
    
    var states = {
        fav: 'favorite.png', //В избранном
        nofav: 'favorite_gray.png',//Не в избранном
        progress: 'loading.gif'//Загрузка
    }
    
    //0 - избранное, 1 - не избранное, 2 - загрузка
    function setState(item, state) {
        var $img = getImg(item).updateImg(state);
        if (state==states.progress) return;
        $img.data('fav', state===states.fav);
    }
    
    function isFav(item) {
        return !!getImg(item).data('fav');
    }

    function updateState(item, toggle) {
        setState(item, isFav(item) ? (!toggle ? states.fav : states.nofav) : (!toggle ? states.nofav : states.fav));
    }
    
    //Инициализируем
    $stars.each(function(){
        updateState(this);
    });
    
    //Слушатели
    
    var inProgress = false;
    $stars.click(function(){
        if(inProgress) return;//---
        inProgress = true;
        
        var $box = getBox(this);
        
        var data = {
            fav: isFav($box) ? 0 : 1,
            type: $box.data('type'),
            ident: $box.data('ident')
        }
        
        setState($box, states.progress);

        AjaxExecutor.execute('PopupFavorites', data,
            function() {
                updateState($box, true);
                PsLocalBus.fireDeferred(PsLocalBus.EVENT.FAVORITES);
                inProgress = false;
            }, 
            function(err) {
                updateState($box);
                InfoBox.popupError(err);
                inProgress = false;
            });
    })
})

/*
 * Добавим фильтр.
 * Принцип простой:
 * 1. Сохраняем оригинальные значения в data
 * 2. Пользуемся для фильтрации :icontains
 * 3. Регулярными выражениями вырезаем подстроки, которые соответствуют значению, введённому в фильтр
 * 4. Оборачиваем вырезанные значения в <span>Текст</span>
 * 
 */
$(function() {
    var FMANAGER = PsFoldingManager.FOLDING('pp', '404');
    var STORE = FMANAGER.store();

    var $boxes = $('.popup-tool');
    
    var getA$ = function(box) {
        return $(box).find('.popup-tool-content h4 a.name');
    }

    var getText$ = function(box) {
        return $(box).find('.popup-tool-content div.text');
    }

    $boxes.each(function() {
        var $a = getA$(this);
        $a.data('text', $a.text());
        var $text = getText$(this);
        $text.data('text', $text.text());
    });
    
    var lastVal = STORE.get('filter', '');
    
    $('#ToolsFilter').keyup(function(){
        var $input = $(this);
        var val = $input.val();
        $boxes.removeClass('hidden');
        if (val) {
            $boxes.not(':icontains($)'.replace('$', val)).addClass('hidden');
        }
        STORE.set('filter', val);
        
        $boxes.not('.hidden').each(function() {
            var $a = getA$(this);
            var $text = getText$(this);
            
            var textA = $a.data('text');
            var textText = $text.data('text');
            
            if (val) {
                //Необходимо эскейпировать специальные символы, которые могут входить в шалон: +, [, ] и т.д.
                var quantifier = PsStrings.regExpQuantifier(val);
                var tokens = (textA+' '+textText).match(new RegExp(quantifier, 'gi'));
                tokens = PsArrays.unique(tokens);
                tokens.walk(function(token) {
                    //Нужно сразу заменять все символы, чтобы не повредить уже заменённые
                    textA = textA.replaceAll(token, PsHtml.span(token, 'selected'));
                    textText = textText.replaceAll(token, PsHtml.span(token, 'selected'));
                });
            }
            
            $a.html(textA);
            $text.html(textText);
        });
        
    }).val(lastVal).keyup();
})
