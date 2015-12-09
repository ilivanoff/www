$(function() {
    var FMANAGER = PsFoldingManager.FOLDING('ap', 'APAudit');
    var $BOX = $('#ap-APAudit');
    
    //alert(FMANAGER.src('img.png'));
    //alert(FMANAGER.store().get('my param', 'my dflt value'));
    //FMANAGER.store().set('my param', 'my value');
    
    /*
     * ПОИСК СТАТИСТИКИ
     */
    var $STAT_BOX = $('#audit-statistic');
    
    var $allButtons = $STAT_BOX.find('button');
    
    var $dateTo = $STAT_BOX.find('.ps-datetime-picker');
    var $statistic = $STAT_BOX.find('.results');
    var $statisticTbody = $statistic.find('tbody');
    var $statisticTotal = $statistic.find('.total');

    //Все кнопки
    var $btnSearch = $STAT_BOX.find('.stat-buttons button:first');
    var $btnReset = $STAT_BOX.find('.stat-buttons button:last');
    var $btnDump = $STAT_BOX.find('.take-dump-buttons button');
    var $btnReloadDumps = $STAT_BOX.find('.dump-buttons button');
    
    //Размер порции дампа
    var dumpSize = strToInt($btnDump.data('size'));

    var nDumpTotal = null;
    var nTimeSearch = null;

    var executeStatistic = function(params, onSuccess) {
        $allButtons.uiButtonDisable();
        AdminAjaxExecutor.execute('AuditStatisticAction', params, function(res) {
            $allButtons.uiButtonEnable();
            onSuccess(res);
        }, function(error) {
            $allButtons.uiButtonEnable();
            InfoBox.popupError(error);
        }, function() {
            //После выполнения действий пересчитаем доступность кнопки 'Снять дамп'
            var canDump = PsIs.number(nDumpTotal) && PsIs.number(nTimeSearch) && (nDumpTotal>=dumpSize) && (nTimeSearch>0 && nTimeSearch<=new Date().getTime()/1000);
            $btnDump.uiButtonSetEnabled(canDump);
        });
    }
    
    //Найти
    $btnSearch.button({
        text: true,
        icons: {
            primary: "ui-icon-search"
        }
    }).click(function() {
        nTimeSearch = strToInt($dateTo.val());
        executeStatistic({
            action: 'search',
            date: nTimeSearch
        }, function(res) {
            nDumpTotal = 0;
            $statisticTbody.empty();
            res.walk(function(item) {
                var $tr = $('<tr>').appendTo($statisticTbody)
                var cnt = strToInt(item['cnt'], 0);
                $tr.append($('<td>').html(item['name']));
                $tr.append($('<td>').html(item['action']));
                $tr.append($('<td>').html(cnt));
                nDumpTotal += cnt;
            });
            $statisticTbody.extractParent('table').trigger(PsEvents.TABLE.modified);
            $statisticTotal.html(nDumpTotal);
            $statistic.show();
        });
    });
    
    //Сбросить
    $btnReset.button({
        text: true,
        icons: {
            primary: "ui-icon-refresh"
        }
    }).click(function() {
        $dateTo.val('').keyup();
        $statistic.hide();
        nDumpTotal = null;
        nTimeSearch = null;
    });
    
    //Снять дамп
    $btnDump.button({
        text: true,
        icons: {
            primary: "ui-icon-disk"
        }
    }).click(function() {
        if (nDumpTotal === null) {
            InfoBox.popupError('Вначале нужно произвести поиск');
            return;//---
        }
        if (nDumpTotal <= 0) {
            InfoBox.popupError('Нет записей для дампа');
            return;//---
        }
        if (nDumpTotal < dumpSize) {
            InfoBox.popupError('Недостаточно записей для дампа');
            return;//---
        }
        if (!nTimeSearch || nTimeSearch<=0) {
            InfoBox.popupError('Поиск произведён без даты');
            return;//---
        }
        if (nTimeSearch!=strToInt($dateTo.val())) {
            InfoBox.popupError('Выполните поиск с выбранной датой');
            return;//---
        }
        var timeDelta = Math.round(new Date().getTime()/1000-nTimeSearch);
        if (timeDelta<0) {
            InfoBox.popupError('Выбранная дата позже текущей');
            return;//---
        }
        PsDialogs.confirm('Подтвердите съём дампа до <b class="nowrap">' + 
            PsTimeHelper.utc2localDateTime(nTimeSearch)+'</b> &mdash; '+
            PsTimeHelper.formatDHMS(timeDelta)+' назад.<br/>Кол-во записей: <b>'+dumpSize+'/'+nDumpTotal+'</b>.', function() {

                executeStatistic({
                    action: 'dump',
                    date: nTimeSearch
                }, function(res) {
                    InfoBox.popupSuccess('Дамп успешно снят');
                    var event = jQuery.Event('click');
                    event.doSearch = true;
                    $btnReloadDumps.trigger(event);
                });

            });
    });
    
    /*
     * ЗАГРУЗКА СТАТИСТИКИ
     */
    //Перезагрузить
    $btnReloadDumps.button({
        text: true,
        icons: {
            primary: "ui-icon-refresh"
        }
    }).click(function(e) {
        executeStatistic({
            action: 'load-dumps'
        }, function(res) {
            $STAT_BOX.find('.db-dumps').replaceWith(res.dumps);
            
            if (e && e.doSearch) {
                $btnSearch.click();
            }
        });
    });

    //Установим базовую доступность кнопок
    $allButtons.uiButtonEnable();
    $btnDump.uiButtonDisable();
    
});