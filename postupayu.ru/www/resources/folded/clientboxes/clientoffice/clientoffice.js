/**
 * Утилиты по управлению нотификацией для маленького личного кабинета пользователя
 * (справа в панели)
 */
var PsOfficeTools = {
    logger: PsLogger.inst('PsOfficeTools').setDebug()/*.disable()*/,
    state: 0,  //0 - ничего не делаем, 1 - загружаем в данный момент, 2 - ошибка
    delay: 5, //Задержка между запросами (секунд)
    
    //Извлекает число из строки: '(+5)' -> 5
    str2num: function(str) {
        str = /\d+/.exec($.trim(str));
        return PsIs.number(str) ? 1 * str : 0;
    },

    //Метод устанавливает кол-во событий
    setCnt: function(ident, cnt, delta) {
        var $href = $('#cb-clientoffice a.stetable.'+ident.ensureStartsWith('ip-'));
        if ($href.isEmptySet()) return; //---
        var byDelta = PsIs.number(delta);
        var numNow = this.str2num($href.find('span.cnt').remove().html());
        var numNew = byDelta ? numNow + delta : this.str2num(cnt);
        if (numNew<=0) return;//Сообщений нет ---
        $href.append(PsHtml.span$('('+(byDelta ? numNew : cnt)+')', 'cnt'));
        if (numNew<=numNow) return;//Мы уменьшили кол-во сообщений или оставили тем-же ---
        //Сбросим страницу, чтобы, когда пользователь по ней кликнет, мы открыли её заново
        PsIdentPagesManager.resetPage(ident);
        //Покажем всплывающее сообщение, по которому пользователь сможет кликнуть
        InfoBox.popupInfo(crA().html('У вас есть новые события').clickClbck(function() {
            InfoBox.popupHide();
            RightPanelController.setVis(true);
            PsScroll.scrollTo('#rightPanel');
        }), 5000).addClass('msg');
    },

    //Функция запрашивает события для текущего пользователя и обновляет офис
    update: function() {
        if (this.state > 0) return;//Сейчас загружаем или произошла ошибка---
        this.state = 1;//Загружаем
        this.logger.logDebug('Запрашиваем события для текущего пользователя');
        AjaxExecutor.execute('LoadUserEvents', {
            ctxt: this
        }, function(events) {
            this.logger.logDebug('События получены: '+PsObjects.toString(events));
            for (var ident in events) {
                this.setCnt(ident, events[ident]);
            }
            this.state = 0;//Готовы загружать
        }, function(err) {
            this.state = 2;//Ошибка
            this.logger.logError('Ошибка загрузки сообщений: '+err);
            this.unsubscribeInterval();//Отпишемся от обновлений
        });
    },
    
    //Метод включает режим постоянного получения событий для пользователя. Эх... с php нам это недоступно:(
    subscribeInterval: function() {
        PsUtil.scheduleDeferred(function() {
            if (this.state==2) return;//Если произошла ошибка, пока мы ждали отложенный режим - не подписываемся
            this.logger.logError('Включена подписка на постоянные обновления событий, задержка: {} секунд.', this.delay);
            PsGlobalInterval.subscribe(this.delay*1000, this.update, this);        
        }, PsOfficeTools);
    },
    
    //Выключает режим постоянной подписки
    unsubscribeInterval: function() {
        PsGlobalInterval.unsubscribe(this.update);
    }
}

$(function() {
    //Логаут
    $('#cb-clientoffice p.exit a').one('click', function(event){
        event.preventDefault();
        $(this).addClass("processing");

        $.get('ajax/Logout.php', function(){
            PsLocalBus.fire(PsLocalBus.EVENT.LOGOUT);
            locationReload();
        });

    });
    
    //Подпишемся на событие получения очков
    PsLocalBus.connect(PsLocalBus.EVENT.POINTSGIVEN, function() {
        PsOfficeTools.update();
    });
    
//PsOfficeTools.subscribeInterval();
});