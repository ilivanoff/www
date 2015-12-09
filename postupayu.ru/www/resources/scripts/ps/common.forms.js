/**
 * Предустановка параметров плагина валидации
 */
if ($.validator) {
    $.validator.setDefaults({
        errorClass: 'error',
        errorElement: 'span',
        onkeyup: false,
        focusInvalid: true,
        focusCleanup: false
    });
    
    jQuery.validator.prototype.ps = function(errors){
        var validator = this;
        var messages = validator.settings.messages;
        //var formId = validator.currentForm.id;
        
        if (typeof(errors)=='undefined'){
            return validator;//---
        }
        
        var errs = {};
        if(typeof(errors)=='object'){
            /*
             * JSON
             */
            $.each(errors, function(id, val) {
                if(typeof(val)!='string') {
                    /*
                     * Обязательно должна быть строка, хоть даже пустая.
                     */
                    return;
                }
                
                if(typeof(messages[id])=='undefined') {
                    /*
                     * Сообщений для элемента нет, поэтому что пришло, то и будет ошибкой.
                     */
                    errs[id]=val;
                }
                else{
                    if(isEmpty(val)) {
                        /*
                         * Ключа ошибки не передано, поэтому полагаемся на то, что в качестве
                         * сообщения для ошибки в жаваскипте назначен текст, а не массив.
                         */
                        errs[id]=messages[id];
                    }
                    else{
                        /*
                         * Проверим, что нам передано - ключ сообщения или само сообщение.
                         */
                        if (typeof(messages[id][val])=='string'){
                            errs[id]=messages[id][val];
                        }
                        else{
                            errs[id]=val;
                        }
                    }
                }
            });
        }
        else if(typeof(errors)=='string') {
            /*
             * Ошибка, обычно возвращаемая сервером в случае непредвиденной ситуации
             */
            var $form = $(validator.currentForm);
            InfoBox.clearMessages($form);
            $form.prepend(InfoBox.divError(errors));
        }
        validator.showErrors(errs);
        
        return validator;
    }
    
    $.validator.addMethod("notex", function(value, element) { 
        return value==null || !value.hasJaxSymbols(); 
    }, "Поле не может содержать формулы");
    
    $.validator.addMethod("notEqualTo", function(value, element, param) {
        return this.optional(element) || value != $(param).val();
    }, "Поле не должно совпадать");

    $.validator.addMethod("exactlength", function(value, element, param) {
        return this.optional(element) || value.length == param;
    }, $.format("Пожалуйста введите ровно {0} символов."));
}

/**
 * Менеджер отслеживает время последнего сохранения формы и, если нужно, заблокирует кнопку сохранения.
 */
var PsFormSubmitTimer = {
    started: false,
    delay: null,
    lastAction : null,
    buttons: $(),
    
    init: function() {
        this.delay = defs.currentSubmitTimeout;
        //Начальное время последнего действия возмём из времени построения страницы,
        //так как непонятно, в какой момент мы дойдём до инициализации данного таймера.
        this.lastAction = PsUtil.getStartTime(true);
    },
    
    reinit: function() {
        if(!this.started) {
            this.delay = defs.ACTIVITY_INTERVAL;
            this.lastAction = getSeconds();
        }
    },
    
    notify: function(timeLeft) {
        this.buttons.disable().val('Пожалуйста, подождите ('+timeLeft+') ...');
    },
    
    finish: function() {
        this.buttons.enable().restoreVal();
        this.started = false;
    },
    
    addButon: function(button){
        if (this.buttons.index(button)==-1) {
            this.buttons = this.buttons.add(button.saveVal());
        }
    },
    
    start: function(button) {
        this.addButon(button);
        if(!this.started) {
            var waitTime = Math.min(1 + this.lastAction + this.delay - getSeconds(), defs.ACTIVITY_INTERVAL);
            this.started = waitTime > 0;
            
            if (this.started) {
                PsGlobalInterval.subscribe4Nseconds(waitTime, function(left) {
                    this.notify(left);
                }, function() {
                    this.finish();
                }, this);
            }
        }
        
        return this.started;
    }
};


/*
 * Менеджер для работы с капчей.
 * На страницу будет добавлена только картинка с id='captchaimage'.
 * Данный менеджер находит этот элемент и загружает в него новую капчу.
 */
var PsCapture = {
    $IMG: null,
    $INPUT: null,
    disabled: false,
    
    //Инициализация работы с капчей
    init: function() {
        this.$IMG = $('#'+CONST.CAPTCHA_IMG_ID);
        this.$INPUT = this.$IMG.siblings('input:text');
        this.disabled = this.$IMG.isEmptySet() || this.$INPUT.isEmptySet();
        if (this.disabled) return; //Если капчи нет, то мы не ожидаем, что она может появиться
        this.$IMG.click(function() {
            /* 
             * Вешаем слушателя на клик по картинке.
             * При этом, если пользователь сам кликнул, но поле ввода задизейблено
             * (например форма в данный момент сабмитится), то мы не будем перезагружать капчу.
             * 
             * Если же не сабмитится, то после загрузки поставим фокус на поле ввода капчи,
             * чтобы пользователь мог ввести текст с картинки
             */
            PsCapture.reset(true, false);
        });
        this.reset(false); //Перезагрузим картинку, без установки фокуса
    },
    
    //Перезагружает капчу
    reset: function(focus, force) {
        //Капчи на странице нет?
        if (this.disabled) return;//---
        
        //Мы сейчас загружаем капчу?
        if (this.$IMG.is('.loading')) return;//----
        
        //Можно ли обновить капчу, если поле ввода задизейблено?
        if (!force && !this.$INPUT.isEnabled()) return;//---
        
        //Для удобства создадим переменные
        var $IMG = this.$IMG.addClass('loading');
        var $INPUT = this.$INPUT.val('');
        var $PROGRESS = span_progress().insertAfter($IMG);
        
        /*
         * Нам важно отменить признак загрузки капчи именно тогда, когда картинка
         * прийдёт с сервера. Для этого нужно подписаться на load и error события,
         * среагировав на них ровно один раз и именно при загрузке нашей картинки.
         */
        var ready = false;
        var onReady = function() {
            if(!ready) return;//Защитимся от того, что события будут вызваны для предыдущего src
            ready = false;
            $IMG.unbind('load').unbind('error');
            if (focus) $INPUT.focus();
            $PROGRESS.remove();
            $IMG.removeClass('loading');
        };
        $IMG.bind('load', onReady).bind('error', onReady);
        ready = true;
        $IMG.attr('src', CONST.CAPTCHA_SCRIPT + '?' + Math.random());
    }
}

/*
 * Менеджер для работы с формами.
 * При добавлении формы на страницу она должна быть загеристрирована в данном менеджере,
 * а он уже выполнит всю необходимую работу.
 */
var FormHelper = {
    active: {},
    deferred: {},
    registerOnce: function(params) {
        var options = {
            form: null, /* string_formId or jQuery */
            single: true, /* После сохранения: true - перезагрузка, false - информация, null - разовое сохранение (обычный submit) */
            autoreset: true, /* Сбрасывать ли форму после каждого сохранения */
            onConfirm: function(button, $form) {
            //Подтверждающий текст. Если ничего не будет возвращено, форма бутед немендленно сохранена.
            },
            msgProgress: function(button) {
                //Текст для замены текста кнопки. Можно передать просто текст.
                return 'Выполняем';
            },
            onInit: function($form) {
            //Методж вызывается после инициализации формы, чтобы с ней можно было выполнить какие-либо действия
            //this=$form
            },
            onOk:  function(ok) {
            //Функция может быть вызвана для показа сообщения об успешности операции и выполнения ряда действий.
            //Если будет передан текст, он будет показан в качестве информации об успешности операции.
            //В случае успешного выполнения и single=true страница будет перезагружена.
            
            //Можно вернуть объект с двумя полями: msg и url. Последний, это адрес, на который пользователь будет 
            //перенаправлен в случае успешного выполнения оперции.
            },
            validator: {
                rules: {},
                messages: {}
            }
        }
        
        $.extend(options, params);

        //Дальнейшие действия выполним после загрузки страницы, чтобы уменишить кол-во использований livequery
        $(function() {
            if (PsIs.string(options.form)) {
                //Передан #formId, формы может ещё не быть на странице
                options.id = options.form.removeFirstCharIf('#');
                options.form = $('#' + options.id);
            }
            else
            {
                //Передан jQuery, форма точно есть
                options.id = options.form.attr('id');
            }
        
            if (options.form.isEmptySet()) {
                //Форма будет обработана в отложенном режиме
                FormHelper.deferred[options.id] = options;
                options.form.livequery(FormHelper._registerDeferred);
            } else {
                FormHelper._registerImpl(options);
            }
        });
    },
    
    /**
     * Слушатели сабмита формы.
     * Они будут вызваны до нотификации основной функции об успешности сохранения формы.
     */
    listeners: {},
    registerListener: function(id, callback, ctxt) {
        this.listeners[id] = this.listeners[id] || [];
        this.listeners[id].push({
            id: id,
            ctxt: ctxt,
            callback: callback
        });
    },
    
    _notifyListeners: function(id, ok) {
        var arr = this.listeners[id] || [];
        arr.walk(function(listener) {
            listener.callback.call(listener.ctxt, ok);
        });
    },
    
    /**
     * Hiddens
     */
    hiddens: {},
    addFormHidden: function(id, name, value) {
        this.hiddens[id] = this.hiddens[id] || {};
        this.hiddens[id][name] = value;
        this._setFormHiddens(id);
    },
    
    _setFormHiddens: function(id) {
        if (!this.active[id] || !this.hiddens[id]) return;//---
        var $form = this.active[id].form;
        $.each(this.hiddens[id], function(k, v) {
            $form.addFormHidden(k, v);
        });
        delete this.hiddens[id];
    },
    
    /*
     * Search
     */
    _search: function($form) {
        var isEnabled = !!$form.data('search');
        var $box = isEnabled ? $('<div>').insertAfter($form) : null;
        return {
            //Сброс результатов поиска
            reset: function() {
                if ($box) $box.hide();
            },
            start: function() {
                if($box) $box.empty().append(loadingMessageDiv()).show();
            },
            //Отображение результатов поиска
            done: function(results) {
                if(!$box) return;//---
                $box.empty();
                //Если передана информация по запросу (для админов), покажем её
                if (results.query) {
                    var $query = $('<div>');
                    $query.append($('<div>').html(results.query).addClass('query'));
                    $query.append($('<div>').html(results.params).addClass('params'));
                    $box.append(PsHtml.hiddenBox$('Запрос', $query, true));
                }
                if(!results.data) {
                    $box.append(noItemsDiv('Ничего не найдено')).show();
                    return;//---
                }
                var $table = $('<table>').addClass('colored indexed sortable database search-results');
                var $thead = $('<thead>').appendTo($table);
                var $tbody = $('<tbody>').appendTo($table);
                var $tr = $('<tr>').appendTo($thead);
                
                var settings = results.settings;
                
                var extract$td = function(row, col) {
                    var value = PsObjects.getValue(row, col, '');
                    var $td = $('<td>');
                    if (value!=='' && value!==null) {
                        PsArrays.toArray(settings[col]).walk(function(prop) {
                            switch (prop) {
                                case 'date':
                                    value = PsTimeHelper.utc2localDateTime(value);
                                    break;
                                case 'pre':
                                    value = '<pre>'+$.trim(value)+'</pre>';
                                    break;
                            }
                            if (prop.startsWith('class-')) {
                                prop = prop.removeFirstCharIf('class-');
                                $td.addClass(prop);
                            }
                        });
                    }
                    return $td.html(value);
                }
                
                results.columns.walk(function(col) {
                    $tr.append($('<th>').html(col));
                });
                results.data.walk(function(obj) {
                    $tr = $('<tr>').appendTo($tbody);
                    results.columns.walk(function(col) {
                        $tr.append(extract$td(obj, col));
                    });
                });
                $box.append($table);
                //Покажем результаты в отложенном режиме, чтобы грид успел проиндексироваться
                PsUtil.scheduleDeferred($box.show, $box, 100);
            },
            //Признак включённости
            isEnabled: function() {
                return isEnabled;
            }
        }
    },
    
    /**
     * Private
     */
    _registerDeferred: function() {
        var $form = $(this);
        var id = $form.attr('id');
        if(!FormHelper.deferred.hasOwnProperty(id)) return;//---
        var options = FormHelper.deferred[id];
        //Переустановим форму, так как на момент инициализации её небыло
        options.form = $form;
        delete FormHelper.deferred[id];
        FormHelper._registerImpl(options);
    },
    
    //Фактическая регистрация формы
    _registerImpl: function(options) {
        //Регистрируем активную форму
        this.active[options.id] = options;
        
        //Элементы формы
        var $form = options.form;
        var $reset = $form.find(':reset');
        var $submit = null; //Кнопка, по которой мы кликнули
        var $submits = $form.find('input:submit'); //Все сабмит-кнопки
        
        if ($form.hasChild('input[type=file]')) {
            $form.attr({
                encoding: 'multipart/form-data',
                enctype:  'multipart/form-data'
            });
        }
        
        //Если у нас несколько кнопок на форме, то необходимо знать - по какой из них мы кликнули
        $submits.click(function() {
            $submit = $(this);
            $form.addFormHidden(defs.FORM_PARAM_BUTTON, $submit.val());
        });
        
        //Функция получения текста подтверждения сохранения
        var onConfirm = function() {
            return $.isFunction(options.onConfirm) ? options.onConfirm($submit.val(), PsUrl.getParams2Obj($form.serialize())) : options.onConfirm;
        }
        
        //Сообщение для прогресса
        var msgProgress = function() {
            return ($.isFunction(options.msgProgress) ? options.msgProgress($submit.val()) : options.msgProgress).ensureEndsWith('...');
        }
        
        //Функция получения сообщения об успешности операции
        var onOk = function(ok) {
            FormHelper._notifyListeners(options.id, ok);
            return $.isFunction(options.onOk) ? options.onOk(ok) : options.onOk;
        }
        
        //Установим hiddens
        this._setFormHiddens(options.id);
        
        //Определим, используется ли tiymer для формы
        var timer = !!$form.data('timer') ? PsFormSubmitTimer : null;
        
        //Инициализируем движок поиска, если он есть для формы
        var SEARCH = this._search($form);
        
        //Единичное или множественное сохранение
        var isSingle = SEARCH.isEnabled() ? false : options.single;
        
        //Сбрасывать ли форму после каждого сохранения
        var isAutoreset = SEARCH.isEnabled() ? false : options.autoreset;
        
        //Валидатор
        var validator = null;
        
        options.validator.errorElement = $form.hasChild('textarea') ? 'div' : 'span';
        options.validator.errorPlacement = function(error, element) {
            element.parent().append(error);
        }
        options.validator.submitHandler = function() {
            //Проверим, определили мы кнопку сабмита
            if (!$submit) {
                return;//---
            }
            
            // ОБРАБОТКА
            if (timer && timer.start($submit)) {
                return;//---
            }
            
            var $set = $form.activeFormInputs();
            
            if (isSingle === null) {
                //Разовое сохранение формы, после которого страница будет перезагружена
                PsDialogs.confirm(onConfirm(), function() {
                    //Меняем текст на кнопке
                    $submit.saveVal(msgProgress()).disable();
                    //Сабмитим форму
                    $form.get(0).submit();
                    //disable - именно здесь, иначе значения не будут переданы при сохранении
                    //Необходимо выполнить его в отложенном режиме, так как данные могут не успеть быть собраны (бага в хроме при логине в xxx)
                    PsUtil.startTimerOnce(function() {
                        $set.disable();
                    }, 10);
                });
                return;//---
            }
            
            var submitOptions = {
                beforeSubmit: function() {
                    //Меняем текст на кнопке
                    $submit.saveVal(msgProgress());
                    
                    //Перенесём дизейблинг поля ввода сюда, т.к. в противном случае значения полей не будут сереализованы.
                    $set.disable();
                    
                    //Удалим информационные сообщения
                    InfoBox.clearMessages($form);
                    
                    //Спрячем результаты поиска и покажем прогресс
                    SEARCH.start();
                },
                success: function(response) {
                    //start success
                    if (isSingle) 
                    {
                        //ОДИНОЧНОЕ СОХРАНЕНИЕ
                        processAjaxResponse(response, 
                            function(ok) {
                                var okMsg = onOk(ok);
                                var okTxt = PsIs.object(okMsg) && okMsg.hasOwnProperty('msg') ? okMsg['msg'] : okMsg;
                                var okUrl = PsIs.object(okMsg) && okMsg.hasOwnProperty('url') ? okMsg['url'] : null;
                                
                                var delay = 0;
                                if (okTxt) {
                                    $form.prepend(InfoBox.divSuccess(okTxt));
                                    delay = 1000;
                                }
                                
                                PsUtil.startTimerOnce(function() {
                                    PsUrl.redirectToPath(okUrl, true);
                                }, delay);
                            },
                            function(error) {
                                validator.ps(error);
                                $set.enable();
                            });
                        
                        $submit.restoreVal();
                    }
                    else
                    {
                        //МНОЖЕСТВЕННОЕ СОХРАНЕНИЕ
                        processAjaxResponse(response, 
                            function(ok) {
                                var okMsg = onOk(ok);
                                if (okMsg) {
                                    var $infoBox = InfoBox.divSuccess(okMsg);
                                    $form.prepend($infoBox);
                                    InfoBox.fadeOut($infoBox, 3);
                                }
                                
                                //Очистим введённые пользователем значения
                                if (isAutoreset) {
                                    $form.get(0).reset();
                                }
                                
                                //Перезагрузим капчу. Поля у нас ещё задизейблены, так что force=true
                                PsCapture.reset(false, true);
                                
                                //Запустим таймер заново
                                if (timer) {
                                    timer.reinit();
                                }
                                
                                //Отобразим результаты поиска
                                SEARCH.done(ok);
                            },
                            function(error) {
                                //Спрячем прогресс
                                SEARCH.reset();
                                //Покажем ошибку на форме
                                validator.ps(error);
                            });
                        
                        $submit.restoreVal();
                        $set.enable();
                    }
                //end success
                }
            };
            
            //Выполняем сабмит, возможно показав сообщение
            PsDialogs.confirm(onConfirm(), function() {
                $form.ajaxSubmit(submitOptions);
            });
        }
        
        //Для textarea, которая ограничена по длине поля, добавим правило валидации
        $form.find('textarea[ml]').each(function() {
            var $texarea = $(this);
            var name = $texarea.attr('name');
            var maxlength = 1 * $texarea.attr('ml');
            
            options.validator.rules[name] = options.validator.rules[name] || {};
            options.validator.rules[name].maxlength = maxlength;
            
            options.validator.messages[name] = options.validator.messages[name] || {};
            options.validator.messages[name].maxlength = 'Максимальная длина поля: $ символов'.replace('$', maxlength);
        });

        //Для форм, требующих ввода капчи
        var captureField = CONST.CAPTCHA_FIELD;
        $form.find('input:text[name="'+captureField+'"]').each(function() {
            options.validator.rules[captureField] = {
                required: true,
                //Мы ещё добавим проверку на длину капчи, чтобы лишний раз не посылать запрос на сервер
                exactlength: CONST.CAPTCHA_LENGTH,
                remote: 'ajax/Pscapture.php'
            };
            
            options.validator.messages[captureField] = 'Требуется валидный код с картинки';
        });
        
        //Вешаем валидатор на форму
        validator = $form.validate(options.validator);
        
        //Добавим слушатель очистки формы
        $reset.click(function() {
            InfoBox.clearMessages($form);
            validator.resetForm();
            $form.activeFormInputsNoButtons().first().focus();
            SEARCH.reset();
        });
        
        //Вызываем метод инициализации (this=$form)
        options.onInit.call($form, $form);
    },
    
    //Действие, которое будет выполнено после :reset
    //Мы позаботимся о единоразовом вызове callback уже после выполнения reset
    bindOnReset: function($form, callback) {
        if(!$.isFunction(callback)) return;//---
        var waiting = false;
        $($form).extractParent('form').bind('reset', function() {
            if(!waiting) {
                waiting = true;
                PsUtil.startTimerOnce(function() {
                    callback();
                    waiting = false;
                }, 10);
            }
        });
    }
}

/*
 * Зависимый комбо-бокс
 * 
 * filter(parentVal, $childOptions)
 */
jQuery.fn.childCombo = function($parentCombo, filter, ctxt) {
    var $child = $(this);
    var $parent = $($parentCombo);
    var onParentChange = function() {
        var value = $parent.val();
        var $enabled = filter.call(ctxt, value, $child.find('option').hide().disable());
        if(!PsIs.empty($enabled)) $enabled.show().enable().selectOption();
    }
    $parent.extractTarget('select').change(onParentChange).keyup(onParentChange);
    FormHelper.bindOnReset($parent, onParentChange);
    PsUtil.scheduleDeferred(onParentChange)
}


$(function() {
    PsFormSubmitTimer.init();
    PsCapture.init();
});