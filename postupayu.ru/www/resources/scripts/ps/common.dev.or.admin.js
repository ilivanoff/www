/* Ресурс подключается только если отключён режим продакшена или пользователь авторизован под админом */

$(function() {
    
    //TODO лист
    PsHotKeysManager.addListener('Ctrl+Alt+T', {
        f: function() {
            popupWindowManager.openWindow('todo');
        },
        descr: 'Список TODO'
    });
    
    
    //Логи клиентсого интерфейса
    PsHotKeysManager.addListener('Ctrl+Alt+D', {
        f: function() {
            var ID = 'LogsList';
        
            var STORE = PsLocalStore.inst('PsJsLogs');
        
            if(!PsDialog.has(ID)) {
                PsDialog.register({
                    id: ID,
                    ctxt: this,
                    build: function(DIALOG, whenDone) {
                        //build
                        var $table = $('<table>').addClass('logs');
                    
                        var $ctrl = $('<div>');
                    
                        var initFilterText = STORE.get('filter.text', '');
                    
                        var $filter = $('<input>').attr({
                            'class': 'filter',
                            'placeholder': 'Фильтр'
                        }).val(initFilterText).keyup(function() {
                            var val = $(this).val();
                        
                            STORE.set('filter.text', val);
                        
                            var $trs = $table.find('tr').show();
                            if (!val) return; //---
                                
                            var quantifier = PsStrings.regExpQuantifier(val);
                            var regexp = new RegExp(quantifier, 'i');
                        
                            $trs.each(function() {
                                var $tr = $(this);
                                if (!regexp.test($tr.text())) {
                                    $tr.hide();
                                }
                            });
                        });
                    
                        var initFilterlevel = STORE.get('filter.level', '');
                    
                        var $level = $('<select>').addClass('level');
                        for(var lvl in PsLogger.LEVELS) {
                            $level.append($('<option>').val(lvl).html(lvl));
                        }
                        $level.val(PsLogger.levelName(initFilterlevel));
                        $level.change(function() {
                            var level = PsLogger.levelName($(this).val());
                            STORE.set('filter.level', level);
                            //TODO - фильтровать отображаемые логи
                            InfoBox.popupInfo('New log level: ' + level);
                        });
                        
                        $ctrl.append($filter).append($level);

                        DIALOG.div.append($ctrl);
                        DIALOG.div.append($table);
                        
                        var hdiv = DIALOG.div[0];
                        
                        PsLogger.addOnLogChangedListener(function(events) {
                            //scrollHeight - Общая высота элемента
                            //offsetHeight - высота видимого содержимого
                            //scrollTop - высота части, оставшейся сверху и невидимой из-за скрола
                            //scrollBottom - высота части, оставшейся снизу и невидимой из-за скрола
                            var scrollBottom = hdiv.scrollHeight - hdiv.offsetHeight - hdiv.scrollTop;
                            $table.append(PsArrays.filter(events, function(logEvent) {
                                var result = {
                                    item: '',
                                    take: true
                                };
                                if (logEvent.prefix && logEvent.level) {
                                    result.item = '<span class="'+logEvent.type+'"><b>'+logEvent.prefix+'</b>:</span> ';
                                }
                                result.item = ['<tr><td>', result.item, logEvent.msgProgress, '</td></tr>'].join('');
                                return result;
                            }).join(''));
                            $filter.keyup();
                            //После показа окна необходимо проскролировать див с логами в самый низ
                            if(!scrollBottom) {
                                DIALOG.div.scrollTop(hdiv.scrollHeight);
                            }
                        });
                        
                        whenDone(DIALOG);
                    },
                    onAfterShow: function(DIALOG) {
                        //При повторном открытии окна "перемотаем" к концу логов
                        //Выполнять нужно в отложенном режиме, так как открытие самого окна может писать лог и вызвать фокус на фильтр
                        PsUtil.startTimerOnce(function() {
                            DIALOG.div.scrollTop(DIALOG.div[0].scrollHeight);
                        }, 5);
                    },
                    wnd: {
                        title: 'Логи клиента',
                        buttons: 'Очистить'
                    },
                    doAction: function(DIALOG, button) {
                        DIALOG.div.find('table').html('');
                    }

                });
            }
        
            PsDialog.toggle(ID);
        },            
        ctxt: null,
        descr: 'Логи клиента',
        enableInInput: true, /* Срабатывать ли в полях ввода */
        stopPropagate: true  /* Прерывать ли событие */
    });

});
