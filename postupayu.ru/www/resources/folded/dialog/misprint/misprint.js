$(function() {
    var FMANAGER = PsFoldingManager.FOLDING('dg', 'misprint');
    var LOGGER = FMANAGER.logger().setDebug();
    
    /*
     * Регистрируем горячую клавишу для открытия окна
     */
    PsFastOpenDialogs.register({
        'Ctrl+Enter': {
            id: 'dg-misprint-dialog',
            ident: 'misprint',
            /*
             * Контекст вызова.
             * Если не указан, то вызов будет произведён в контексте данного объекта
             */
            //ctxt: null,
            /*
             * Настройки для HotKeyManager'а
             */
            hotkey: {
                descr: 'Сообщить об опечатке или неточности (требуется выделить текст)',
                enableInInput: false, //Отключаем в полях ввода
                stopPropagate: true   //Прерываем событие
            },
            /*
             * Настройки самого окна
             */
            wnd: {
                title: 'Нашли опечатку?',
                buttons: 'Отправить'
            },
            /*
             * Функция вызывается при нажатии горячей клавиши и позволяет отменить дальнейшую обработку
             */
            preHotKey: function(DIALOG) {
                if (PsDialog.isOpenAny()) {
                    LOGGER.logTrace('Есть открытые окна, пропускаем');
                    return false;//---
                }
        
                var text = $.trim(copySelection());
                if(!text) {
                    InfoBox.popupWarning('Не выделен текст');
                    return false;//---
                }
        
                if (text.length > 255) {
                    InfoBox.popupWarning('Выделен слишком большой объем текста');
                    return false;//---
                }
                
                DIALOG.data = text;
        
                return true;
            },
            /*
             * Функция обратного вызова для загрузки содержимого окна
             */
            build: function(onDone, DIALOG) {
                DIALOG.div.
                append($('<div>').addClass('content')).
                append($('<h5>').html('Примечание (не обязательно):')).
                append($('<textarea>'));
                onDone();
            },
            /*
             * Функция вызывается после загрузки и добавления содержимого окна в DIALOG.div
             */
            onAdd: function(DIALOG) {
                
            },
            /*
             * Функция вызывается при показе окна
             */
            onShow: function(DIALOG) {
                DIALOG.div.find('.content').html(DIALOG.data.htmlEntities());
                DIALOG.div.find('textarea').val('');
            },
            /*
             * Функция вызывается при нажатии на кнопку
             */
            saving: false,
            doAction: function(DIALOG, text) {
                DIALOG.close();
                var note = $.trim(DIALOG.div.find('textarea').val());
                InfoBox.popupSuccess('Спасибо за участие');
                
                if (this.saving) {
                    LOGGER.logInfo('Выполняется предыдущее сохранение, пропускаем обработку...');
                    return;//---
                }
                this.saving = true;
                
                AjaxExecutor.executePost('Misprint', {
                    ctxt: this,
                    url: defs.url,
                    text: DIALOG.data,
                    note: note
                }, function() {
                    //OK
                    LOGGER.logInfo('Сохранение выполнено успешно');
                }, function(err) {
                    //ERR
                    LOGGER.logError('Сохранение выполнено с ошибкой: ' + err);
                }, function() {
                    //COMMON
                    this.saving = false;
                });
            }
        }
    });
    
});