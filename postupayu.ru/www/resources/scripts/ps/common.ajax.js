/* Основные методы для разбора ajax-ответа от сервера.
 * Пример работы с методами:
 *
    var result = ajax_success(response);
    if (result) {
        //... do on success
    }
    else
    {
        var error = ajax_error(response);
        //... do on error
    }
 */
function ajax_result(data){
    try
    {
        var _json = data;
        
        if(!PsIs.object(_json)) {
            _json = $.parseJSON(_json);
        }
        
        var bSuccess = !!(_json && _json.hasOwnProperty('res'));
        var success = null;
        var error = null;
        
        if (bSuccess) {
            success = _json['res'];
        }
        else
        {
            error = _json && _json.hasOwnProperty('err') ? _json['err'] : data;
        //error = isEmpty(error) ? '' : '' + error;
        }
        
        return {
            ok: bSuccess,
            res: success,
            err: error
        };
    }    
    catch(e) {
        return {
            ok: false,
            res: null,
            err: data /*isEmpty(data) ? '' : '' + data*/
        };
    }
}

/*
var $res = ajax_result(null);
for(i in $res){
    alert(i+'='+$res[i] + ' ' + typeof($res[i]));
}
*/

function ajax_success(data){
    return ajax_result(data).res;
}

function ajax_error(data/*or XMLHttpRequest*/) {
    if (PsIs.object(data) && data.hasOwnProperty('status') && data.hasOwnProperty('statusText') && data.hasOwnProperty('responseText')){
        return 'Запрос выполнен с ошибкой '+data.status+' ['+data.statusText+']:<br/>'+data.responseText;
    }
    return ajax_result(data).err;
}

/*
 * Пример разбора ответа сервера.
 *
$.get('ajax/Action.php', {}, function(response){
    processAjaxResponse(response, function(ok) {
        //Process OK
        }, function(error) {
        //Process ERR
        })
});
*/
function processAjaxResponse(response, successCallback, errorCallback, ctxt) {
    var ret = null;
    
    var result = ajax_result(response);
    if (result.ok) 
    {
        ret = successCallback.call(ctxt, result.res);
    }
    else
    {
        var isByTimeout = isString(result.err) && (!result.err || result.err.indexOf('Maximum execution time of') != -1);
        
        if ($.isFunction(errorCallback)) {
            ret = errorCallback.call(ctxt, result.err, isByTimeout);
        }
        else if(PsIs.string(errorCallback)) {
            InfoBox.popupError('Ошибка выполнения действия ['+errorCallback+'], причина:<br/>' + 
                (isByTimeout ? 'прервано по таймауту' : result.err));
        }
        else if(PsIs.jQuery(errorCallback)) {
            errorCallback.replaceWith(span_error(isByTimeout ? 'Прервано по таймауту' : result.err));
        }
        else
        {
            InfoBox.popupError('Ошибка выполнения действия:<br/>' + 
                (isByTimeout ? 'прервано по таймауту' : result.err));
        }
    }
    
    return ret;
}

/*
AjaxExecutor.execute('Action', {
    action: 'save'
}, function(ok) {
    return 1;
}, function(err) {
    return 2;
}, function(num) {
    InfoBox.popupInfo(num);
}); 
*/

var AjaxExecutor = {
    cnt: 0,
    cur: null,
    progress: false,
    logger: PsLogger.inst('AjaxExecutor').setDebug()/*.disable()*/,
    shedules: [],
    sheduleHashes: {},
    //#1
    __executeImpl: function() {
        if (this.progress || this.shedules.length==0) return;//---
        this.progress = true;
        
        var SECUNDOMER = new PsSecundomer();
        
        var options = $.extend({
            num: -1,
            data: {},
            dataStr: '{}',
            url: 'ajax/Action.php',
            ctxt: null,
            type: 'GET',
            action: null,
            clbcOk: null,
            clbcErr: null,
            clbcAfter: null
        }, this.shedules.shift());

        this.cur = options.num;
        this.logger.logInfo('> Запрос #{} на {} {} [{}] отправлен, параметры: {}', options.num, options.url, options.type, options.action, options.dataStr);
        
        options.data[defs.AJAX_ACTION_PARAM] = options.action;
        
        var processResp = function(response) {
            this.logger.logInfo('< Запрос #{} на {} {} [{}] выполнен за {} секунд.', options.num, options.url, options.type, options.action, SECUNDOMER.stop());
            
            try {
                if($.isFunction(options.clbcOk)) {
                    var ret;
                    if (options.clbcErr)
                    {
                        ret = processAjaxResponse(response, options.clbcOk, options.clbcErr, options.ctxt);
                    }
                    else
                    {
                        ret = options.clbcOk.call(options.ctxt, response);
                    }
                    if ($.isFunction(options.clbcAfter)) {
                        options.clbcAfter.call(options.ctxt, ret);
                    }
                }
            } catch(err) {
                this.logger.logError('Запрос #{} обработан с ошибкой: {}.', options.num, err);
                throw err;
            }
            
            /*
             * Запускаем цепочку выполнения. Это - важный момент!
             * Мы делаем новый запрос не тогда, когда получили ответ, а тогда, когда отработал обработчик этого ответа!
             */
            this.cur = null;
            this.progress = false;
            this.__executeImpl();
        }
        
        /*
         * Непосредственное обращение к серверу.
         * Мы не ограничиваем timeout, так как всё ограничение по времени происходит на сервере.
         */
        SECUNDOMER.start();
        
        $.ajax({
            data: options.data,
            url: options.url,
            type: options.type,
            timeout: 0,
            success: function(response) {
                processResp.call(AjaxExecutor, response);
            },
            error: function(xhr) {
                processResp.call(AjaxExecutor, ajax_error(xhr));
            }
        });
    },
    //#1
    
    /**
     * Метод проверяет, был ли такой запрос выполнен.
     * Позволяет избежать дублирования запросов на сервер.
     */
    isExecuted: function(action, data) {
        return this.sheduleHashes.hasOwnProperty(action+'|'+this.dataToString(data));
    },
    
    /**
     * Преобразует данные, которые будут отправлены на сервер, в строку.
     * Используется при построении кеша запроса, а также для внешнего логирования.
     */
    dataToString: function(data) {
        return PsObjects.toString(PsObjects.clone(data||{}, ['ctxt']));
    },
    
    /**
     * Все запросы, выполняемые через ajax, выполняются последовательно.
     * Данный метод ставит запрос на выполнение в очеред запросов.
     */
    scheduleExecute: function(options) {
        options.data = options.data || {};
        
        var ctxt = options.data['ctxt'];
        delete options.data['ctxt'];
        
        options.num = ++this.cnt;
        options.ctxt = ctxt;
        options.dataStr = PsObjects.toString(options.data);
        options.url = options.url ? options.url : 'ajax/Action.php';
        
        this.shedules.push(options);
        this.sheduleHashes[options.action+'|'+options.dataStr] = true;
        
        //Если сейчас выполняется запрос, то выведем информацию в лог о добавлении запроса в очередь
        if (this.progress) {
            this.logger.logInfo('! Запрос #{} на {} {} [{}] запланирован (выполняется #{}), параметры: {}.', options.num, options.url, options.type, options.action, this.cur, options.dataStr);
        }
        this.__executeImpl();
    },
    
    /**
     * Выполняет GET запрос на сервер.
     */
    execute: function (action, data, callback, callbackErr, callbackAfter) {
        this.scheduleExecute({
            data: data,
            type: 'GET',
            action: action,
            clbcOk: callback,
            clbcErr: callbackErr,
            clbcAfter: callbackAfter
        });
    },
    
    /**
     * Выполняет POST запрос на сервер. Нужен для передачи больших данных.
     */
    executePost: function (action, data, callback, callbackErr, callbackAfter) {
        this.scheduleExecute({
            data: data,
            type: 'POST',
            action: action,
            clbcOk: callback,
            clbcErr: callbackErr,
            clbcAfter: callbackAfter
        });
    },
    
    loadScript: function(url, callback, ctxt) {
        var LOGGER = this.logger;
        var NUMBER = ++this.cnt;
        LOGGER.logInfo('Запрос #{}, подключаем скрипт: [{}].', NUMBER, url);
        
        var SECUNDOMER = new PsSecundomer(true);
        
        var processResp = function(isOk) {
            LOGGER.logInfo('Запрос #{} выполнен за {} секунд. {}.', NUMBER, SECUNDOMER.stop(), isOk ? 'Скрипт успешно подключён' : 'Скрипт не подключён');
            if ($.isFunction(callback)) {
                callback.call(ctxt, isOk);
            }
        }
        
        $.ajax({
            url: url,
            dataType: 'script',
            success: function() {
                processResp(true);
            },
            error: function() {
                processResp(false);
            }
        });
    }
}
