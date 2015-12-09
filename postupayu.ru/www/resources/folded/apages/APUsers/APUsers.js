/*
 * Переписка с пользователями
 */

$(function(){
    
    var STORE = PsLocalStore.ADMIN;
    var KEY = 'last-user-story';
    
    var lastUserId = STORE.get(KEY);
    PsJquery.onHrefClick($('.user_card_control>a.history'), function($href, userId) {
        STORE.remove(KEY);

        var $box = $href.extractParent('.user-box');
        //Переведём скролл на карточку пользователя
        PsScroll.scrollTo($box, 'fast');
        
        var $historyHolder = $('.feed-history-holder');
        if(!$historyHolder.isEmptySet()){
            $historyHolder.remove();
            if (lastUserId==userId) {
                return;//---
            }
        }
        lastUserId = userId;
        STORE.put(KEY, lastUserId);
        
        $historyHolder = $('<div>').addClass('feed-history-holder').
        append(loadingMessageDiv('Загружаем историю переписки')).
        insertAfter($href.parent());

        AdminAjaxExecutor.execute('FeedbackAction', {
            id: userId,
            action: 'load'
        }, function(ok) {
            $historyHolder.html('').append($(ok));
            //Переведём скролл на карточку пользователя после добавления истории, 
            //так как места теперь больше
            PsScroll.scrollTo($box, 'fast');
        }, 'Загрузка истории')
    });
    
    //Попробуем открыть последнюю историю. Если не получится, спозиционируемся на пользователя, написавшего сообщения
    if (lastUserId) {
        $('.user_card_control>a.history[href="#'+lastUserId+'"]').click();
    } else {
        PsScroll.scrollTo($('.new-msgs:first'));
    }
});
