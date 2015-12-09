/*
 *  ===================
 *  = ДИАЛОГОВЫЕ ОКНА =
 *  ===================
 */

/*
PsDialog.register({
    id: null,
    ctxt: null,
    build: function(DIALOG, whenDone) {
        //build
        whenDone(DIALOG);
    },
    onShow: function(DIALOG) {
        
    },
    onAfterShow: function(DIALOG) {
    
    },
    doAction: function(DIALOG, $button) {
                
    },
    wnd: {
        title: null,
        buttons: []
    }
});
 */

var PsDialog = {
    logger: PsLogger.inst('PsDialog').setTrace()/*.disable()*/,
    
    dialogs: {},
    
    has: function(id) {
        return this.dialogs.hasOwnProperty(id);
    },
    
    isOpen: function(id) {
        return this.has(id) && this.dialogs[id] && this.dialogs[id].div && this.dialogs[id].div.uiDialogIsOpen();
    },
    
    isOpenAny: function() {
        for (var id in this.dialogs) {
            if (this.isOpen(id)) {
                return true;
            }
        }
        return false;
    },
    
    close: function(id) {
        if (this.isOpen(id)) {
            this.dialogs[id].div.uiDialogClose();
        }
    },
    
    closeAll: function() {
        PsObjects.keys2array(this.dialogs).walk(function(id) {
            this.close(id);
        }, false, this);
    },
    
    toggle: function(id, data) {
        if (this.isOpen(id)) {
            this.close(id);
        } else {
            this.open(id, data);
        }
    },
    
    wndDefault: {
        width: 'auto',
        height: 'auto',
        resizable: false,
        draggable: false,
        closeText: 'Закрыть',
        closeOnEscape: true,
        buttons: [],
        autoOpen: false,
        stack: false,
        dialogClass: 'ps-dialog',
        modal: true
    },
    
    wndOpts: function(id, wndOpts) {
        if(!this.has(id)) return;
        if(!PsIs.object(wndOpts)) return;
        
        $.extend(this.dialogs[id].wnd, wndOpts);
        
        if (this.dialogs[id].div) {
            this.dialogs[id].div.dialog("option", wndOpts);
        }
    },
    
    register: function(options) {
        var id = options.id;
        
        if (this.has(id)) {
            this.logger.logTrace('Возвращаем экземпляр окна [{}]', id);
            return this.dialogs[id].DIALOG;
        }
        
        this.logger.logInfo('Регистритуем диалоговое окно с кодом: {}', id);
        
        options = $.extend({
            id: null,
            ctxt: null,
            
            //Вызывается для построения содержимого окна
            build: function(DIALOG, whenDone) {
                whenDone(DIALOG);
            },
            
            //Вызывается ДО показа окна
            onShow: function(DIALOG) {
            },
            
            //Вызывается ПОСЛЕ показа окна
            onAfterShow: function(DIALOG) {
            },
            
            //Выполнение действия по кнопке
            doAction: function(DIALOG, button) {
            
            },
            
            //Параметры окна, их можно переопределить
            wnd: {
                title: null,
                buttons: []
            },
            
            DIALOG: {
            
            },
            
            //PRIVATE
            //Состояние: 0 - не загружалось, 1 - загрузилось успешно, 2 - ошибка
            div: null,
            state: 0,
            error: null
        }, options);
        
        //ПАРАМЕТРЫ ОКНА
        options.wnd = $.extend({}, this.wndDefault, options.wnd);
        
        //КНОПКИ
        var buttons = {};
        var actionCallback = function(event) {
            options.doAction.call(options.ctxt, options.DIALOG, $(event.target).text());
        }
        
        PsArrays.toArray(options.wnd.buttons).walk(function(i) {
            buttons[i] = actionCallback;
        });
        
        options.wnd.buttons = buttons;
        
        //DIALOG
        options.DIALOG.id = id;
        options.DIALOG.div = null;
        options.DIALOG.shows = 0;
        
        options.DIALOG.open = function(data) {
            PsDialog.open(id, data);
            return options.DIALOG;
        };
        
        options.DIALOG.close = function() {
            PsDialog.close(id);
            return options.DIALOG;
        };
        
        options.DIALOG.isOpen = function() {
            return PsDialog.isOpen(id);
        };
        
        options.DIALOG.toggle = function(data) {
            PsDialog.toggle(id, data);
            return options.DIALOG;
        };
        
        options.DIALOG.wndOpts = function(wndOpts) {
            PsDialog.wndOpts(id, wndOpts);
            return options.DIALOG;
        }
        
        options.DIALOG.title = function(title) {
            PsDialog.wndOpts(id, {
                title: title
            });
            return options.DIALOG;
        };
        
        //Сохраним настройки
        this.dialogs[id] = options;
        
        return options.DIALOG;
    },
    
    showStack: [],
    
    um: new PsUpdateModel(),
    
    /*
     * Открытие окна.
     * 
     * force - окно будет открыто в не зависимости от того, показано оно сейчас или нет.
     * first - позволяет дорбавить окно первым в очередь на открытие. 
     *         Нужно для того, чтобы открывать окно просто повторным вызовом метода open 
     *         после подготовки всех данных в методе doOpen.
     */
    open: function(id, data, force, first) {
        if (!this.has(id)) return;//---
        
        force = !!force;
        first = !!first;
        
        var ob = {
            id: id,
            data: data,
            force: force
        }
        
        this.logger.logDebug('Поступила заявка на открытие окна [{}], force={}, first={}', id, force, first);
        
        if (first) {
            this.showStack.unshift(ob);
        } else {
            this.showStack.push(ob);
        }
        
        this.doOpen();
    },
    
    doOpen: function() {
        if (this.um.isStarted() || this.showStack.length==0) return;//---
        
        var ob = this.showStack.shift();
        var id = ob.id;
        var data = ob.data;
        var force = ob.force;
        
        if (!force && this.isOpen(id)) {
            this.doOpen();
            return;//---
        }
        
        var options = this.dialogs[id];
        
        var title = options.wnd.title;
        
        this.um.start();
        
        this.closeAll();
        
        //Данные установим, только если они действительно передавались
        if (PsIs.defined(data)) {
            options.DIALOG.data = data;
        }
        
        var _this = this;
        switch (options.state) {
            case 0:
                //LOAD
                this.logger.logInfo('Начинаем загрузку окна [{}] ({})', id, title);
                
                //Покажем окно загрузки с заголовком и параметрами будущего окна
                var $divProgress = $('<div>').attr('id', 'ps-dialog-progress-box');
                var $wndProgress = $('<div>').append($divProgress.append(loadingMessageDiv())).appendTo('body').
                dialog($.extend({}, this.wndDefault, {
                    title: title ? title : 'Построение окна',
                    minHeight: 100
                })).uiDialogOpen();
                
                var $div = $('<div>').attr('id', id);
                var $wnd;
                options.DIALOG.div = $div;
                
                this.um.start();
                options.build.call(options.ctxt, options.DIALOG, PsUtil.once(function(DIALOGorERR) {
                    if (options.DIALOG === DIALOGorERR) {
                        _this.logger.logInfo('Окно [{}] загружено успешно. Параметры отображения: [{}]', id, PsObjects.toString(options.wnd));
                        
                        options.state = 1;
                        
                        //Необходимо обернуть наш див в другой, чтобы ширина дива была auto
                        //К сожалению, как выяснилось, если задать ширину самому диву, то она будет 
                        //проигнорирована
                        $wnd = $('<div>').append($div).hide().appendTo('body').dialog(options.wnd);
                    } else {
                        _this.logger.logError('Окно [{}] загружено с ошибкой: {}', id, DIALOGorERR);
                        
                        options.state = 2;
                        options.error = DIALOGorERR;
                        
                        $div = null;
                        $wnd = null;
                    }
                    options.div = $wnd;                        
                    options.DIALOG.div = $div;
                    
                    $wndProgress.uiDialogClose().remove();
                    
                    _this.um.stop();
                    _this.open(id, data, force, true);
                }));
                break;
            case 1:
                //OK
                ++options.DIALOG.shows;
                this.logger.logTrace('Показываем окно [{}] в {} раз', id, options.DIALOG.shows);
                options.onShow.call(options.ctxt, options.DIALOG);
                options.div.uiDialogOpen();
                options.onAfterShow.call(options.ctxt, options.DIALOG);
                break;
            case 2:
                //ERR
                if (options.error) {
                    InfoBox.popupError(options.error);
                }
                break;
        }
        
        this.um.stop();
        this.doOpen();
    }
}

/**
 * Служебные окна - подтверждение, прогресс и т.д.
 */
var PsDialogs = {
    confirm: function(text, onOk, ctxt) {
        onOk = PsUtil.once(onOk, ctxt);
        
        if (text) {
            PsDialog.register({
                id: 'ps-dialog-confirm',
                onShow: function(DIALOG) {
                    DIALOG.div.html(DIALOG.data.msg);
                },
                doAction: function(DIALOG, button) {
                    DIALOG.close();
                    if (button=='Да') {
                        DIALOG.data.onOk();
                    }
                },
                wnd: {
                    title: 'Подтверждение',
                    buttons: ['Да', 'Нет'],
                    width: '400',
                    height: 'auto'
                }
            }).open({
                msg: text,
                onOk: onOk
            });
        } else {
            //Не передан текст - выполняем действие без подтверждения
            onOk();
        }
    },
    
    progress: function(title) {
        return PsDialog.register({
            id: 'ps-dialog-progress',
            build: function(DIALOG, whenDone) {
                DIALOG.div.append(loadingMessageDiv());
                whenDone(DIALOG);
            }
        }).wndOpts({
            minHeight: 100,
            title: title
        }).open();
    }
}

/**
 * "Быстро открываемые" диалоговые окна.
 * В данном менеджере они регистрируются после подключения ресурсов сущностей фолдинга DialogManager.
 */
var PsFastOpenDialogs = {
    dialogs: {},  //Зарегистрированные процессоры для страницы
    logger: PsLogger.inst('PsFastOpenDialogs').setTrace()/*.disable()*/,
    
    /* 
     * Метод вызывается из .js файлов фолдинга DialogManager для регистрации диалоговых окон.
     */
    register: function(dialogs) {
        for(var HotKey in dialogs) {
            var SETTINGS = dialogs[HotKey];
            this.logger.logInfo('Зарегистрировано диалоговое окно \'{}\' [{}] ({})', SETTINGS.id, HotKey, SETTINGS.hotkey.descr);
            this.dialogs[HotKey] = SETTINGS;
        }
    },
    
    /*
     * Метод подключает все зарегистрированные диалоговые окна.
     * При этом регистрация самих диалоговых окон может также идти внутри функции $(function() {}),
     * поэтому, помимо прочего, мы ещё используем отложенный режим.
     */
    init: function() {
        PsUtil.scheduleDeferred(function() {
            for(var HotKey in this.dialogs) {
                //Сохраним ссылку на настроки
                var SETTINGS = this.dialogs[HotKey];
                //Сотрём элемент
                delete this.dialogs[HotKey];
                //Вызываем функцию создания окна (чтобы порадить замыкание)
                this.makeDialog(HotKey, SETTINGS);
            }
        }, this);
    },
    
    makeDialog: function(HotKey, SETTINGS) {
        var LOGGER = this.logger;
        //Проверим, не зарегистрировано ли уже окно с этим id
        if (PsDialog.has(SETTINGS.id)) {
            LOGGER.logError('Диалоговое окно {} уже зарегистрировано!', SETTINGS.id);
            return;//---
        }
        //Метод для вызова обратных функций из SETTINGS
        var SettingsCall = function(name, params) {
            var f = SETTINGS[name];
            var result = {
                called: false,
                result: null
            }
            if ($.isFunction(f)) {
                LOGGER.logTrace('Вызываем [{}] для окна [{}]', name, SETTINGS.id);
                result.called = true;
                result.result = f.apply(PsObjects.getValue(SETTINGS, 'ctxt', SETTINGS), PsArrays.toArray(params));
            }
            return result;
        }
        /* 
         * Создавая окно мы будем использовать замыкание, так как мы ранее убедились, что такого окна нет.
         */
        var DIALOG = PsDialog.register({
            id: SETTINGS.id,
            build: function(DIALOG, whenDone) {
                /*
                 * Загружаем содержимое окна. Если в настройках есть функция build,
                 * то вызовем её, иначе обратимся к серверу.
                 */
                var onDivLoaded = PsUtil.once(function(content, isError) {
                    if (isError) {
                        //ERROR
                        whenDone(content);
                    } else {
                        //SUCCESS
                        /*
                         * Добавим загруженное содержимое к диву.
                         * Его может и не быть, в build мы могли добавлять содержимое 
                         * непосредственно в DIALOG.div
                         */
                        if (content) DIALOG.div.append(content);
                        //Вызовем функцию на добавление
                        SettingsCall('onAdd', DIALOG);
                        //Оповестим окно об окончании загрузки
                        whenDone(DIALOG);
                    }
                });
                
                //Начинаем загрузку...
                try {
                    if (SettingsCall('build', [onDivLoaded, DIALOG]).called) {
                    /*
                     * У объекта есть метод build, который построит содержимое окна.
                     * Результат будет возвращён вызовом метода onDivLoaded.
                     */
                    } else {
                        /*
                         * Обратимся за содержимым окна на сервер
                         */
                        AjaxExecutor.execute('DialogWindowContent', {
                            ident: SETTINGS.ident
                        }, function(content) {
                            onDivLoaded(content, false);
                        }, function(err) {
                            onDivLoaded(err, true);
                        });
                    }
                } catch(e) {
                    onDivLoaded(e, true);
                }
                
            },
            onShow: function(DIALOG) {
                //Вызовем функцию на показ
                SettingsCall('onShow', DIALOG)
            },
            doAction: function(DIALOG, text) {
                //Вызовем функцию на нажатие кнопки
                SettingsCall('doAction', [DIALOG, text]);
            },
            wnd: SETTINGS.wnd
        });
        
        //Регистрируем HotKey
        var HotKeySettings = PsObjects.clone(SETTINGS.hotkey, [], true);

        HotKeySettings.f = function() {
            if (DIALOG.isOpen()) {
                DIALOG.close();
                return;//----
            }

            var callPre = SettingsCall('preHotKey', DIALOG);
            if(!callPre.called || callPre.result) {
                DIALOG.open();
            }
        }
        
        PsHotKeysManager.addListener(HotKey, HotKeySettings);
        
        LOGGER.logInfo('Создано диалоговое окно \'{}\' [{}] ({})', SETTINGS.id, HotKey, SETTINGS.hotkey.descr);
    }
}

jQuery(function() {
    PsFastOpenDialogs.init();
});
