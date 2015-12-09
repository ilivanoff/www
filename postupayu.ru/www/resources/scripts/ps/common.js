var PsEvents = {
    TABLE: {
        sorted: 'TableSorted', //Таблица была отсортирована по столбцу
        modified: 'TableModified' //Была добавлена/удалена строка
    }
}

//Включаем логгер в зависимости от настроек
PsLogger.logConsole = defs.isLogging;

/* 
 * Менеджер, отображающий все глобальные ошибки
 * 
 * ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯
 *             +++++++++|
 *             + popup +|
 *             +++++++++|
 */
var PsGlobalExceptionHandler = {
    box: null,
    waits: [],
    loading: false,
    format: '%m<div class="file"><a href="%u" target="_blank" class="nobg">%f</a> (%l)</div>',
    onError: function(msg, url, line) {
        var self = this;
        var file = url.substr(url.lastIndexOf('/')+1, url.length);
        self.waits.push([msg, url, file, line]);
        
        //Ошибки могут возникнуть до того, как будет построен html и мы сможем их отобразать, поэтому отложим их.
        if (self.loading) {
            return;//---
        }
        
        var onDone = function() {
            for(var i=0; i<self.waits.length; i++) {
                var msg = self.waits[i][0];
                var url = self.waits[i][1];
                var file = self.waits[i][2];
                var line = self.waits[i][3];
                var formatted = self.format.replace('%m', msg).replace('%u', url).replace('%f', file).replace('%l', line);
                self.box.children('ol').append($('<li>').html(formatted));
            }
            self.waits = [];
        }
        
        if(!self.box) {
            self.loading = true;
            $(function() {
                self.box = $('<div>').attr('id', 'errorWindow').
                append($('<a>').addClass('close').html('X').click(function() {
                    self.box.remove();
                    self.box = null;
                    return false;
                })).
                append($('<h5>').html('Ошибки javascript')).
                append($('<ol>')).
                appendTo('body');
                self.loading = false;
                onDone();
            });
        } else {
            onDone();
        }
    },
    
    connect: function() {
        if (!defs.isDOA) return;//---
        window.onerror = function(msg, url, line) {
            PsGlobalExceptionHandler.onError(msg, url, line);
            return false;
        }
    }
}

PsGlobalExceptionHandler.connect();

/*
 * Утилиты для работы с url
 */
var PsUrl = {
    
    //Разбивает строку get запроса и формирует объект
    getParams2Obj: function(get) {
        if (PsIs.object(get)) {
            return get; //Передан объект
        }
        
        get = getStringEnd($.trim(get), '?', true);
        
        var pairs = get.split('&');
        var ob = {};
        pairs.walk(function(pair) {
            var kv = pair.split('=');
            var k = kv[0];
            var v = kv.length>0 ? kv[1] : null;
            ob[k] = v;
        });
        return ob;
    },
    
    //Склеивает параметры get запроса из объекта
    obj2getParams: function(data){
        if (PsIs.string(data)) {
            return data; //Передана строка
        }
        
        data = data || {};
        
        var params = [];
        jQuery.each(data, function(key, val) {
            if (data.hasOwnProperty(key)) {
                params.push(key+'='+val);
            }
        });
        
        return params.join('&');
    },
    
    uniqueUrlParams: function(url) {
        if(!url.contains('=')) {
            return url;
        }
        
        var tokens = url.split('?', 2);
        var base = tokens.length == 2 ? tokens[0] : null;
        var params = tokens.length == 2 ? tokens[1] : tokens[0];
        
        var result = [];
        var added = {};
        params.split('&').reverse().walk(function(pair) {
            var kv = pair.split('=');
            var k = kv[0];
            if (added[k]) return;
            added[k] = true;
            result.push(pair);
        });
        
        return (base ? base + '?' : '') + result.reverse().join('&');
    },
    
    addParams: function(baseUrl, params, sub) {
        params = $.trim(PsIs.object(params) ? this.obj2getParams(params) : params);
        baseUrl = $.trim(baseUrl);
        var delim = baseUrl ? (baseUrl.contains('?') ? '&' : '?') : '';
        sub = sub ? sub.ensureStartsWith('#') : '';
        return this.uniqueUrlParams(baseUrl + delim + params + sub);
    },
    
    //lesson.php?post_id=6#p3
    locationUrl: function (href){
        href = href ? $(href).attr('href') : window.location.href;
        return getStringEnd(href, '/', true);
    },
    
    //lesson.php?post_id=6
    locationUrlNoHash: function(href){
        return getStringStart(this.locationUrl(href), '#', true);
    },
    
    //Перенаправляет браузер на указанный путь
    redirectToPath: function(path, doReloadAnyway) {
        path = $.trim(path);
        doReloadAnyway = !!doReloadAnyway;
        
        if (!path) {
            if (doReloadAnyway) {
                location.reload();
            }
            return doReloadAnyway;//---
        }
        
        // http://p.ru/addr.php?p1=v1&p2=v2
        // --------base--------?---params---
        
        var base = '';
        var params = '';
        
        if (path.contains('?')) {
            path = path.split('?', 2);
            base = path[0];
            params = path[1];
        } else if (path.contains('=')) {
            params = path;
        } else {
            base = path;
        }
        
        base = base ? base : location.pathname;
        base = new RegExp('(ftp|http|https):\/\/', 'gi').test(base) ? base : base.ensureStartsWith('/');
        
        params = this.uniqueUrlParams(params);
        params = params ? '?' + params : params;
        
        var finalUrl = base + params;
        
        if (!doReloadAnyway && location.href.endsWith(finalUrl)) {
            return false;
        }
        
        window.location.href = finalUrl;
        return true;
    },
    
    redirectToPathAddParams: function(params, sub) {
        return this.redirectToPath(this.addParams(location.href, params, sub));
    },
    
    /*
     * Обычная ссылка, это та, у которой помимо якоря есть ещё адрес, например:
     * page1.php или page2.php?param1=1#2
     */
    getUsualHref: function(a) {
        var urlNoHash = this.locationUrlNoHash(a);
        return urlNoHash && urlNoHash.contains('.') ? urlNoHash : null;
    },
    
    isUsualHref: function(a) {
        return !!this.getUsualHref(a);
    },
    
    /*
     * Считаем, что ссылка указывает на другую страницу, если у неё задан 
     * полный путь (а не только якорь) и он отличается от текущего пути
     */
    isUsualHref2AnotherPage: function(a) {
        var usualHref = this.getUsualHref(a);
        return usualHref && (usualHref != this.locationUrlNoHash());
    }
}

/*
 * Менеджер скроллинга окна
 * todo - перенести сюда скроллинг
 */

var PsScrollManager = {
    wndScroll: null,
    storeWndScroll: function() {
        var $wnd = $(window);
        this.wndScroll = {
            top: $wnd.scrollTop(),
            left: $wnd.scrollLeft()
        }
    },
    restoreWndScroll: function() {
        if(!this.wndScroll) return;
        var $wnd = $(window);
        $wnd.scrollTop(this.wndScroll.top);
        $wnd.scrollLeft(this.wndScroll.left);
    }
}

/*
 * Хранилище объектов
 */
function ObjectsStore() {
    var OBJECTS = {};
    
    var _obKey4null = PsRand.string(10, false, true);
    var _obKey = function(key) {
        return isEmpty(key) ? _obKey4null : key;
    }
    
    this.has = function(key) {
        return OBJECTS.hasOwnProperty(_obKey(key));
    }
    
    this.hasAll = function(key) {
        key = $.isArray(key) ? key : [key];
        for (var i = 0; i < key.length; i++) {
            if(!this.has(key[i])) return false;
        }
        return true;
    }
    
    this.hasOneOf = function(key) {
        key = $.isArray(key) ? key : [key];
        for (var i = 0; i < key.length; i++) {
            if(this.has(key[i])) return true;
        }
        return false;
    }
    
    this.get = function(key, def) {
        return this.has(key) ? OBJECTS[_obKey(key)] : (isDefined(def) ? def : null);
    }
    
    this.set = function(key, val) {
        OBJECTS[_obKey(key)] = val;
    }
    
    this.put = function(key, val) {
        this.set(key, val);
    }
    
    this.remove = function(key) {
        delete OBJECTS[_obKey(key)];
    }
    
    this.reset = function() {
        OBJECTS = {};
    }
    
    this.clear = function() {
        OBJECTS = {};
    }
    
    this.each = function(callback, ctxt) {
        for (var key in OBJECTS) {
            callback.call(ctxt, key, OBJECTS[key]);
        }
    }
    
    this.doIfHas = function(key, callback) {
        if(!this.has(key)) return;
        callback(this.get(key));
    }
    
    this.putToArray = function(key, val) {
        if(!this.has(key)) {
            this.put(key, []);
        }
        this.get(key).push(val);
    }
    
    this.hasInArray = function(key, val, comparator) {
        var arr = this.get(key);
        return PsIs.array(arr) && arr.contains(val, comparator);
    }
    
    this.putToObjectsStore = function(key1, key2, val) {
        if(!this.has(key1)) {
            this.put(key1, new ObjectsStore());
        }
        this.get(key1).put(key2, val);
    }
    
    this.getFromObjectsStore = function(key1, key2, def) {
        return this.has(key1) ? this.get(key1).get(key2, def) : (isDefined(def) ? def : null);
    }
    
    this.keys = function() {
        return PsObjects.keys2array(OBJECTS);
    }
    
    this.count = function() {
        return this.keys().length;
    }
    
    this.isEmpty = function() {
        return !PsObjects.hasKeys(OBJECTS);
    }
}

/*
 * Адаптер для работы с нативным интервалом
 * 
 * callOnStart - признак, который показывает, нужно ли сразу вызывать callback во время старта, 
 * или (как нативный интервал), вызывать только по прошествии некоторого времени.
 */
function PsIntervalAdapter (callback, delay, callOnStart, ctxt) {
    var interval = null;
    var execute = function() {
        callback.call(ctxt);
    }
    
    this.start = function() {
        return interval ? this : this.restart();
    }
    
    this.restart = function() {
        this.stop();
        
        if (callOnStart) {
            execute();
        }
        interval = setInterval(execute, delay);
        return this;
    }
    
    this.stop = function() {
        if (interval) {
            clearInterval(interval);
            interval = null;
        }
        return this;
    }
}

/*
 * Глобальный интервал. 
 * Используется для того, чтобы не нужно было заводить множество интервалов,
 * так как это серьёзно нагружает систему.
 */
var PsGlobalInterval = {
    logger: PsLogger.inst('PsGlobalInterval').setDebug()/*.disable()*/,
    
    callbacks: new ObjectsStore(),
    intervals: new ObjectsStore(),
    
    /*
     * В callbacks хрянится информация о callbasks, вызываемых в интервалах.
     * Данный компоратор позволяет найти данные, соответствующие callback.
     */
    comparator: function(callback, ob) {
        return callback === ob.clbc;
    },
    
    /*
     * Метод подписывания callback на вызов с интервалом delay.
     * ctxt - контекст вызова.
     * callback на вход получит кол-во интервалов, прошедших от начала подписывания.
     * 
     * Пример - если интервал уже существовал, то в момент регистрации '+' callback будет вызван с 0,
     * а в следующий моент времени будет вызван с 1 и т.д.
     * |-----|-+---|-----|-----|-----|-----|
     * 
     */
    subscribe: function(delay, callback, ctxt) {
        if (this.callbacks.has(delay) && this.callbacks.get(delay).contains(callback, this.comparator)) {
            return;//---
        }
        //Подготовим объект с данными для вызова callback
        var ob = {
            cnt: 0,         //Кол-во вызовов callback
            ctxt: ctxt,     //Контенкст вызова для callback
            clbc: callback, //Функция вызова - callback.call(ctxt, past)
            ready: false    //Признак - была ли функция вызвана в первый раз
        };
        //Подготовим интервал
        if(!this.intervals.has(delay)) {
            this.intervals.put(delay, new PsIntervalAdapter(function() {
                this.callbacks.get(delay).walk(function(ob) {
                    if (ob.ready) {
                        ob.clbc.call(ob.ctxt, ++ob.cnt);
                    }
                });
            }, delay, false, this));
            this.logger.logInfo('Запущен интервал для задержки: {}', delay);
        }
        //Добавим callback к интервалам данного delay
        this.callbacks.putToArray(delay, ob);
        //Попробуем запустить интервал (он может уже работать)
        this.intervals.get(delay).start();
        this.logger.logDebug('Добавлен подписчик {} для задержки: {}', this.callbacks.get(delay).length, delay);
        /*
         * Вызовем callback в первый раз.
         * Вызов данной функции может привести к unsubscribe, поэтому мы предприняли ряд действий:
         * 1. Сначала добавили интервал и запустили его(чтобы можно было отписать и остановить интервал)
         * 2. Ввели признак ready, чтобы мы первыми сделали вызов данной функции, а не интервал
         */
        ob.clbc.call(ob.ctxt, 0);
        ob.ready = true;
    },
    
    /*
     * Отписывает функцию из всех интервалов, в которые она входит
     */
    unsubscribe: function(callback) {
        this.callbacks.each(function(delay, arr) {
            if (!arr.contains(callback, this.comparator)) return;//---
            arr.removeValue(callback, this.comparator);
            this.logger.logDebug('Удалён подписчик {}, для задержки: {}', arr.length+1, delay);
            if (arr.length) return;//---
            this.intervals.get(delay).stop();
            this.logger.logInfo('Остановлен интервал для задержки: {}', delay);
        }, this);
    },
    
    /*
     * Подписывает на две функции с вызовом раз в секунду на кол-во секунд, равное seconds.
     * clbkCountDown - функция вызывается через секунду, получая на вход кол-во оставшихся секунд и кол-во прошедших секунд.
     * clbcFinal - функция вызывается по прошествии seconds секунд.
     */
    subscribe4Nseconds: function(seconds, clbkCountDown, clbcFinal, ctxt) {
        seconds = Math.round(seconds);
        var doProcess = function(past) {
            var last = past >= seconds;
            if (last) {
                //Первым делом - отпишемся
                this.unsubscribe(doProcess);
            }
            if (clbkCountDown) {
                clbkCountDown.call(ctxt, seconds - past, past);
            }
            if (last && clbcFinal) {
                clbcFinal.call(ctxt);
            }
        
        }
        this.subscribe(1000, doProcess, this);
    }
}

/*
 * Адаптер для работы с нативным таймером
 * Временную задержку можно передать либо в конструкторе, либо потом во время старта.
 */
function PsTimerAdapter(callback, delay, ctxt) {
    var timer = null;
    var _delay = delay;
    var _this = this;
    
    var stop = function() {
        if (timer) {
            clearTimeout(timer);
            timer = null;
        }
        return _this;
    }
    
    var start = function(delay) {
        stop();
        
        var time = PsIs.number(delay) ? delay : _delay;
        if(!PsIs.number(time)) return this;//---
        time = time<0 ? 0 : time;
        
        timer = setTimeout(function() {
            //Сразу нужно установить timer=null, так как внутри callback мы можем стартовать таймер снова
            timer = null;
            if ($.isFunction(callback)) {
                callback.call(ctxt);
            }
        }, time);
        
        return _this;
    }
    
    var flush = function() {
        if (timer) {
            stop();
            if ($.isFunction(callback)) {
                callback.call(ctxt);
            }
        }
        return _this;
    }
    
    this.start = start;
    this.stop = stop;
    this.flush = flush;
}


/*
 * UpdateModel
 */
function PsUpdateModel(ctxt, startImpl, stopImpl) {
    var counter = 0;
    var _action;
    
    this.start = function(action) {
        ++counter;
        if (counter==1 && $.isFunction(startImpl)) {
            _action = action;
            startImpl.call(ctxt, _action);
        }
    };
    
    this.stop = function() {
        --counter;
        if (counter==0 && $.isFunction(stopImpl)) {
            stopImpl.call(ctxt, _action);
        }
    };
    
    this.stopDeferred = function(){
        PsUtil.startTimerOnce(function(){
            this.stop();
        }, 700, this);
    },
    
    this.isStarted = function(){
        return counter > 0;
    };
}


/*
 * Secundomer
 * Время возвращается в миллисекундах
 */
function PsSecundomer(autostart) {
    var startTime;
    var totalTime = 0;
    var started = false;
    
    var passedTime = function() {
        return started ? Math.max(0, new Date().getTime() - startTime) : 0;
    }
    
    var current = function() {
        return PsMath.round((totalTime + passedTime())/1000, 2);
    }
    
    var startImpl = function() {
        startTime = new Date().getTime();
        started = true;
    }
    
    var stopImpl = function() {
        totalTime += passedTime();
        started = false;
    }
    
    var UM = new PsUpdateModel(this, startImpl, stopImpl);
    
    this.start = UM.start;
    this.stop = function() {
        UM.stop();
        return current();
    }
    this.time = current;
    
    if (autostart) {
        this.start();
    }
}


function locationReload() {
    PsHotKeysManager.process('F5');
    location.reload();
}

var PsTimeHelper = {
    parseSeconds: function(seconds) {
        var secFull = Math.abs(Math.round(seconds));
        var minFull = Math.floor(secFull / 60);
        var hourFull = Math.floor(minFull / 60);
        var days = Math.floor(hourFull / 24);
        
        var sec = secFull - minFull * 60;
        var min = minFull - hourFull * 60;
        var hour = hourFull - days * 24;
        
        return {
            d: days,
            h: hour,
            hs: this.padZero(hour),
            hf: hourFull, /*Полное отступление в часах*/
            m: min,
            ms: this.padZero(min),
            mf: minFull, /*Полное отступление в минутах*/
            mfs: this.padZero(minFull),
            s: sec,
            ss: this.padZero(sec)
        };
    },
    
    getGmtPresentation: function(offsetInSeconds) {
        var sign = offsetInSeconds < 0 ? '-' : '+';
        var offsetARR = this.parseSeconds(offsetInSeconds);
        return 'GMT ' + sign + offsetARR.hf + ':'+offsetARR.ms;
    },
    
    getDatepickerPresentation: function(offsetInSeconds) {
        var sign = offsetInSeconds < 0 ? '-' : '+';
        var offsetARR = this.parseSeconds(offsetInSeconds);
        return sign + offsetARR.hs + offsetARR.ms;
    },
    
    padZero: function(num, len) {
        len = len ? len : 2;
        var str = '' + num;
        while (str.length < len) {
            str = '0' + str;
        }
        return str;
    },
    
    formatMS: function(seconds) {
        var time = this.parseSeconds(seconds);
        return time.mfs + ':' + time.ss;
    },
    
    formatHMS: function(seconds) {
        var time = this.parseSeconds(seconds);
        return time.hf + ':' + time.ms + ':' + time.ss;
    },
    
    formatDHMS: function(seconds) {
        var time = this.parseSeconds(seconds);
        return (time.d > 0 ? time.d+'д ' : '') + time.hs + ':' + time.ms + ':' + time.ss;
    },
    
    utc2localDateTime: function(seconds) {
        if(!PsIs.number(seconds)) return seconds;//---
        var date = new Date(1000 * seconds);
        return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
    }
}

$.fn.valEnsureIsNumber = function(def) {
    return strToInt($(this).val(), def);
}

$.fn.attrsMap = function() {
    var attributes = {}; 
    if(this.length) {
        $.each(this[0].attributes, function( index, attr ) {
            attributes[ attr.name ] = attr.value;
        } ); 
    }
    return attributes;
}

$.fn.htmlEnsureIsNumber = function(def) {
    return strToInt($(this).text(), def);
}

$.fn.ensureIdIsSet = function(prefix) {
    $(this).each(function() {
        var $el = $(this);
        if(!$el.attr('id')) {
            $el.attr('id', PsRand.pseudoId(prefix));
        }
    });
    return this;
}

/*
 * Модификаторы
 */
jQuery.expr[":"].icontains = jQuery.expr.createPseudo(function (arg) {                                                                                                                                                                
    return function (elem) {                                                            
        return jQuery(elem).text().toUpperCase().indexOf(arg.toUpperCase()) >= 0;        
    };                                                                                  
});

/*
 * Расширения jQuery
 */
jQuery.fn.confirmableClick = function(options) {
    PsJquery.onHrefClick($(this), $.isFunction(options) ? {
        yes: options
    } : options);
}

jQuery.fn.extractParent = function(selector) {
    return this.is(selector) ? this.filter(selector) : this.parents(selector+':first');
}

jQuery.fn.extractTarget = function(selector) {
    return this.is(selector) ? this.filter(selector) : this.find(selector);
}

jQuery.fn.extractHrefsByAnchor = function(anchor) {
    return this.extractTarget('a').filter('[href="#%"]'.replace('%', anchor));
}

jQuery.fn.isEmptySet = function(){
    return this.size()==0;
}

jQuery.fn.hasChild = function(selector) {
    return selector ? !jQuery(this).find(selector).isEmptySet() : !jQuery(this).children().isEmptySet();
}

jQuery.fn.isChildOf = function(selector) {
    return !$(selector).find(this).isEmptySet();
}

jQuery.fn.setChecked = function(checked) {
    return this.prop('checked', checked);
}

jQuery.fn.isChecked = function() {
    return this.prop('checked');
}

jQuery.fn.disable = function(){
    return this.prop('disabled', true);
}

jQuery.fn.enable = function(){
    return this.prop('disabled', false);
}

jQuery.fn.setEnabled = function(enabled){
    return enabled ? this.enable() : this.disable();
}

jQuery.fn.isEnabled = function() {
    return !this.prop('disabled');
}

jQuery.fn.isRequired = function() {
    return !!this.prop('required');
}

//Как показала практика, лучше пользоваться именно этим способом, а не просто делать $select.val()
jQuery.fn.selectOption = function(value) {
    var $options = $(this).extractTarget('option');
    (PsIs.defined(value) ? $options.filter('[value="'+value+'"]') : $options).first().prop('selected', true);
    return this;
}


jQuery.fn.saveVal = function(newVal) {
    return this.each(function(){
        var el = $(this);
        if (!isDefined(el.data('original_value'))) {
            el.data('original_value', el.val());
        }
        if (isDefined(newVal)) {
            el.val(newVal);
        }
    });
}

jQuery.fn.restoreVal = function(){
    return this.each(function(){
        var el = $(this);
        el.val(el.data('original_value'));
    });
}

jQuery.fn.getOriginalVal = function(){
    return this.data('original_value');
}

jQuery.fn.saveHtml = function(newVal){
    return this.each(function(){
        var el = $(this);
        el.data('original_html', el.html());
        if(!isEmpty(newVal)){
            el.html(newVal);
        }
    });
}

jQuery.fn.restoreHtml = function(){
    return this.each(function(){
        var el = $(this);
        el.html(el.data('original_html'));
    });
}

jQuery.fn.getFullHtml = function() {
    return $('<div>').append($(this[0]).clone()).remove().html();
}

jQuery.fn.updateImg = function(newImg){
    return this.each(function(){
        var el = $(this);
        if(el.is('img')){
            var src = el.attr('src');
            if(src){
                var curImgname = getStringEnd(src, '/');
                src = src.replace(curImgname, newImg);
                el.attr('src', src);
            }
        }
    });
}

jQuery.fn.reverse = [].reverse;

jQuery.fn.sort = function() {  
    return this.pushStack( [].sort.apply(this, arguments), []);  
};

/*
 * http://css-tricks.com/snippets/jquery/shuffle-dom-elements/
 * $('ul#list li').shuffle();
 */
jQuery.fn.shuffle = function() {
    var shuffled = [];
    this.get().shuffle().walk(function(el) {
        shuffled.push($(el).clone(true)[0]);
    });
    
    this.each(function(i){
        $(this).replaceWith($(shuffled[i]));
    });
    
    return $(shuffled);
}

//Возвращает значение css свойства в виде числа (100px -> 100)
//Если не удастся определить размер, то будет возврашено dflt
jQuery.fn.cssDimension = function(name, dflt) {
    var val = PsStrings.trim($(this).css(name));
    while(val && !PsIs.number(val)) {
        val = val.removeLastChar();
    }
    return strToInt(val, dflt);
}

//Из-за бага http://bugs.jquery.com/ticket/11584 не будем учитывать margins в Chrome и IE, так как
//в случае auto будет возвращено значение, установленное браузером, чего во многих случаях делать не следует.
jQuery.fn.psOuterWidth = function(includeMargins) {
    return this.outerWidth(includeMargins && !$.browser.chrome && !$.browser.msie);
}

//Позиционирует элемент относительно курсора мыши
//Можно позиционировать относительно элемента, передав в качестве event - jQuery элемент
jQuery.fn.calculatePosition = function(event, offsetX, offsetY) {
    var byEvent = !PsIs.jQuery(event);
    
    offsetX = PsIs.number(offsetX) ? offsetX : (byEvent ? 20 : 0);
    offsetY = PsIs.number(offsetY) ? offsetY : (byEvent ? 5 : 0);
    
    var $div = $(this);
    
    var pageX, pageY; //Точка, относительно которой необходим показ
    var trgtWidth = 0, trgtHeight = 0; //Ширина и высота элемента, на который наведён курсор (для !byEvent)
    if (byEvent) {
        pageX = event.pageX;
        pageY = event.pageY;
    } else {
        var $target = $(event);
        var toffset = $target.offset();
        trgtWidth = $target.psOuterWidth(false);
        trgtHeight = $target.outerHeight(false);
        pageX = toffset.left + trgtWidth;
        pageY = toffset.top + trgtHeight;
    }
    
    var divWidth = $div.psOuterWidth(true);
    var divHeight = $div.outerHeight(true);
    var winWidth = $(window).width();
    var winHeight = $(window).height();
    var winLeft = $(window).scrollLeft();
    var winTop = $(window).scrollTop();
    
    var left = pageX + offsetX; //Факчитеское расстояние до точки показа по горизонтали (с учётом сдвига)
    if (left + divWidth > winLeft + winWidth) {
        var newLeft = left - 2*offsetX - divWidth - trgtWidth;
        if (newLeft < winLeft) {
            //Выяснилось, что новая позиция левого края также не помещается в экран
            //Вычислим ширину видимой области для обоих положений и сравним их.
            //Если в старом варианте (left) видно больше, чем в новом, то оставим старый вариант.
            var xbounds1 = PsMath.bounds(left, left+divWidth, winLeft, winLeft+winWidth);
            var xvis1 = xbounds1[1] - xbounds1[0];
            var xbounds2 = PsMath.bounds(newLeft, newLeft+divWidth, winLeft, winLeft+winWidth);
            var xvis2 = xbounds2[1] - xbounds2[0];
            if (xvis1 < xvis2) {
                left = newLeft;
            }
        } else {
            left = newLeft;
        }
    }
    
    var top = pageY + offsetY; //Факчитеское расстояние от верха страницы до курсора по вертигаки (с учётом сдвига)
    if ((top + divHeight > winTop + winHeight) && (top > divHeight)) { //Если при показе элемента вверх он также не влезает, то покажем его вниз
        top = top - 2*offsetY - divHeight - trgtHeight;
    }
    
    $div.css('left', left).css('top', top);
    
    return this;
}

/*
 * Подбор цвета.
 * Автоматически проверяем, что цвет не совпадает с предыдущим
 */

jQuery.fn.PsColorPicker = function(callback, pickerDefault) {
    return this.each(function(){
        var $input = $(this).val('');
        var color = pickerDefault ? pickerDefault : "#000000";
        $input.colorPicker({
            pickerDefault: color
        });
        $input.change(function() {
            if (color!= $input.val()) {
                color = $input.val();
                callback.call($input, color);
            }
        });
    });
}


/**
 * Метод устанавливает размер элементу, содержащему картинку или спрайт.
 * Размер вычисляется из размеров всех дочерних элементов и установленных для них 
 * параметров min-width.
 */
jQuery.fn.fetchWidth = function(onDone) {
    function elWidth($el, takeBorders) {
        var width = $el.cssDimension('min-width', 0);
        if ($el.is('img') || $el.is('.sprite')) {
            width = Math.max(width, $el.width());
        }
        
        var chWidth = 0;
        $el.children().each(function() {
            chWidth = Math.max(chWidth, elWidth($(this), true));
        });
        
        return Math.max(width, chWidth) + (takeBorders ? /*margin+border+padding*/ $el.psOuterWidth(true) - $el.width() : 0);
    }
    
    /*
     * Для блоков, содержащих картинку или спрайт, вычислим ширину, так как
     * картинка должна полностью влезать в блок.
     */
    this.filter(':has(img, .sprite)').each(function() {
        var $el = $(this);
        
        PsJquery.onLoad($el, function() {
            var needResize = [];
            
            //Нужно собрать все картинки/спрайты, размер которых больше размера родительского контейнера
            $el.find('img, .sprite').each(function() {
                var $img = $(this);
                if ($img.width() >  $img.parent().width()) {
                    needResize.push($img);
                }
            });
            
            if (needResize.length > 0) {
                //Установим ширину самого элемента, изходя из ширины дочерних элементов
                $el.width(elWidth($el, false));
                //Теперь для всех предков карники сбросим размер
                needResize.walk(function($img) {
                    $img.parentsUntil($el).css('width', 'auto').css('min-width', 'auto');
                });
            }
            
            if ($.isFunction(onDone)) onDone.call($el);
        });
    
    });
    
    //this.find('*').css('width', 'auto')
    
    return this;
}


jQuery.fn.appendToBody = function(onDone) {
    //Прячем, аттачим, фиксируем ширину, показываем, позицианируем
    return this.appendTo('body').show().fetchWidth(onDone);
};


jQuery.fn.replaceWith=function(html){
    return this.after(html).remove();
}

jQuery.fn.addFormHidden = function(inputName, value){
    var $form = this.extractTarget('form');
    var $field = jQuery("[name="+inputName+"]", $form).val(value);
    if ($field.isEmptySet()) {
        $('<input>').attr({
            type: 'hidden',
            name: inputName,
            value: value
        }).prependTo($form);
    }
    return this;
}

jQuery.fn.setFormValue = function(inputName, value){
    return this.each(function () {
        jQuery("[name="+inputName+"]", this).val(value)
    });
}

jQuery.fn.getFormValue = function(inputName){
    return jQuery("[name="+inputName+"]", this).val();
}

jQuery.fn.getFormId = function(){
    return $(this).getFormValue(defs.FORM_PARAM_ID);
}

jQuery.fn.isVisible = function(){
    return this.is(':visible');
}

jQuery.fn.setVisible = function(visible){
    return this.each(function (){
        if(visible){
            jQuery(this).show();
        }
        else{
            jQuery(this).hide();
        }
    });
}

jQuery.fn.setVisibility = function(visible){
    return this.each(function (){
        jQuery(this).css('visibility', visible ? 'visible' : 'hidden');
    });
}

jQuery.fn.setVisibleInline = function(visible){
    return this.each(function (){
        if(visible){
            jQuery(this).css('display', 'inline');
        }
        else{
            jQuery(this).hide();
        }
    });
}

jQuery.fn.toggleVisibility = function(){
    return this.each(function (){
        var $el = jQuery(this);
        $el.setVisible(!$el.isVisible());
    });
}

jQuery.fn.toggleVisibilityInline = function(){
    return this.each(function (){
        var $el = jQuery(this);
        $el.setVisibleInline(!$el.isVisible());
    });
}

/*
 *  Возвращает обёрнутый набор всех активных элементов формы.
 */
jQuery.fn.activeFormInputs = function(){
    return jQuery("textarea, input:not([type='hidden']), select", this).filter(':enabled');
}

jQuery.fn.activeFormInputsNoButtons = function(){
    return jQuery("textarea, input:visible:not(:submit, :reset), select", this).filter(':enabled');
}

jQuery.fn.imgSrc = function(src){
    return this.each(function(){
        if ($(this).attr('src')!=src) {
            $(this).attr('src', src);
        }
    });
}

jQuery.fn.backgroundImageUrl = function(options) {
    if (options){
        return this.css('backgroundImage','url('+options+')');
    }
    else {
        return this.css('backgroundImage').replace(/url\(|\)|"|'/g, "");
    }
}

jQuery.fn.clickClbck = function(callback, ctxt) {
    return this.click(function(e) {
        e.preventDefault();
        var $item = $(this);
        //Проверим, возможно элемент disabled - для корректного отключения ссылок (например sitemap)
        //Если не передан callback, то ничего не делаем - нужно для того, чтобы использовать clickClbck как заглушку
        if(!$item.isEnabled() || !callback) return; //---
        //Если кликнули по ссылке внутри ссылки - пропускаем
        if(this!=e.target && $item.is('a') && $(e.target).is('a')) return; //---
        callback.call(ctxt ? ctxt : $item, $item.is('a') ? getHrefAnchor($item) : $item, $item);
    });
}

/*Диапазон значений для слайдера*/
jQuery.fn.sliderRange = function($rangeCtt, updateOnly) {
    return this.each(function(){
        var $sl = $(this);
        var $sr = $sl.next('.slider_range');
        
        var min = PsMath.round($sl.slider("option", "min"), 2);
        var max = PsMath.round($sl.slider("option", "max"), 2);
        
        if (updateOnly && !isEmpty($sr)) {
            $sr.children('span.min').html(min);
            $sr.children('span.max').html(max);
            return;//---
        }
        $sr.remove();
        
        $sr = $('<div>').addClass('slider_range').
        append($('<span>').addClass('min').html(min)).
        append($('<span>').addClass('max').html(max)).
        insertAfter($sl);
        
        if(!isEmpty($rangeCtt)) {
            $sr.append($rangeCtt);
        }
        $sr.append($('<div>').addClass('clearall'));
    });
}

/*
 * Загрузчик файлов с помощью flash
 */
jQuery.fn.psUploadify = function(options) {
    var $input = $(this);
    
    var onError = function(condition, description) {
        var error = isEmpty(condition);
        if (error) $input.replaceWith(InfoBox.divError(description));
        return error;
    }
    if (onError(PsIs.object(options), 'В psUploadify не переданы options')) return $input;//---
    if (onError(PsIs.object(options.postData), 'В psUploadify не переданы options.postData')) return $input;//---
    if (onError(options.postData.hasOwnProperty('type'), 'В psUploadify не передан postData.type')) return $input;//---
    if (onError(defs.marker, 'В psUploadify не передан postData.type')) return $input;//---
    
    options.postData.marker = defs.marker;
    
    var params = {
        'swf'  : '/resources/scripts/uploadify/uploadify.swf',
        'uploader'    : '/ajax/FileUpload.php',
        'file_post_name': defs.FORM_PARAM_FILE,
        'postData'    : {//Должна обязательно быть переопределена извне
            type: 'X',
            marker: defs.marker
        },
        'cancelImage' : '/resources/scripts/uploadify/uploadify-cancel.png',
        'buttonText' : 'Загрузить',
        'fileSizeLimit': CONST.UPLOAD_MAX_FILE_SIZE+'B',//Размер задаётся в байтах
        'removeTimeout' : 0,
        'checkExisting': false,
        'multi'          : false,
        'auto'           : true,
        'requeueErrors'  : false,
        'fileTypeExts'   : '*.gif;*.jpg;*.jpeg;*.png',
        'onUploadComplete' : function(event, data, c, d) {
            InfoBox.popupSuccess('Загрузка файла завершена');
        },
        'onUploadSuccess' : function(event, data) {
            InfoBox.popupSuccess('Файл успешно загружен');
        },
        'onUploadError'  : function(file, errorCode, errorMsg) {
            InfoBox.popupError('Произошла ошибка загрузки ['+errorMsg+']');
        }
    }
    
    return $input.uploadify($.extend(params, options));
};

//Устанавливает для всех илементнов одинаковую ширину/высоту
jQuery.fn.sameWidth = function() {
    return this.css('width', '').width(PsMath.max($.map(this, function(el) {
        return $(el).width();
    })));
}

jQuery.fn.sameHeight = function() {
    return this.css('height', '').height(PsMath.max($.map(this, function(el) {
        return $(el).height();
    })));
}


jQuery.fn.checkBoxesGroup = function() {
    var $chBoxes = this.extractTarget(':checkbox');
    var $selected = $chBoxes.filter(':checked').first();
    $chBoxes.setChecked(false);
    $selected.setChecked(true);
    $chBoxes.change(function() {
        $chBoxes.not(this).setChecked(false);
    });
    return this;
}

//Функции для ui
//КНОПКИ
jQuery.fn.uiButtonDisable = function() {
    try{
        return this.button('disable');
    }catch(e){
    //TODO
    //alert(e);
    }
}

jQuery.fn.uiButtonEnable = function() {
    return this.button('enable');
}

jQuery.fn.uiButtonIsEnabled = function() {
    return !this.button("option", "disabled");
}

jQuery.fn.uiButtonSetEnabled = function(enabled) {
    return enabled ? this.uiButtonEnable() : this.uiButtonDisable();
}

jQuery.fn.uiButtonLabel = function(label) {
    return this.button("option", "label", label);
}

jQuery.fn.uiButtonSetText = function(text) {
    return this.button("option", "text", !!text);
}

jQuery.fn.uiButtonIcons = function(primary, secondary) {
    if(!isDefined(primary) && !isDefined(secondary)) {
        return this.button("option", "icons");
    }
    
    return this.button("option", "icons", {
        primary: primary ? primary : null,
        secondary: secondary ? secondary : null
    });
}

jQuery.fn.uiButtonStoreLabel = function() {
    if(!this.data('ui-button-stored-label')) {
        this.data('ui-button-stored-label', this.button("option", "label"));
    }
    return this;
}

jQuery.fn.uiButtonRestoreLabel = function() {
    return this.uiButtonLabel(this.data('ui-button-stored-label'));
}

jQuery.fn.uiButtonConfirm = function(options) {
    options = $.isFunction(options) ? {
        callback: options
    } : options;
    
    options = $.extend({
        ctxt: null,
        yes: 'Да',
        no: 'нет',
        callback: function(){
            InfoBox.popupError('Callback not implemented');
        }
    }, options);
    
    var $button = this.extractTarget('button');
    
    $button.click(function() {
        if(!$button.uiButtonIsEnabled()) {
            return;//---
        }
        
        var $yes = $('<button>').html(options.yes).click(function() {
            $button.show();
            $repl.remove();
            options.callback.call(options.ctxt, $button);
        }).button();
        
        var $no = $('<button>').html(options.no).click(function() {
            $button.show();
            $repl.remove();
        }).button();
        
        var $repl = $('<span>').append($yes).append($no).buttonset().insertAfter($button.hide());
    });
    
    return this;
}

//СОРТИРОВКА
jQuery.fn.uiSortableDisable = function() {
    return this.sortable("disable");
}

jQuery.fn.uiSortableEnable = function() {
    return this.sortable("enable");
}

jQuery.fn.uiSortableIsEnabled = function() {
    return !this.sortable("option", "disabled");
}

jQuery.fn.uiSortableSetEnabled = function(enabled) {
    return enabled ? this.uiSortableEnable() : this.uiSortableDisable();
}


//Диалоги
jQuery.fn.uiDialogClose = function() {
    return this.dialog('close');
}

jQuery.fn.uiDialogOpen = function() {
    return this.dialog('open');
}

jQuery.fn.uiDialogIsOpen = function() {
    return this.dialog("isOpen");
}

//Прогресс
jQuery.fn.psProgressbarUpdate = function(total, current) {
    var $BODY = $(this).extractTarget('.ps-progress');
    
    var $info = $BODY.children('.ps-progress-info');
    var $pct = $info.find('span.pct');
    var $total = $info.find('span.total');
    var $current = $info.find('span.current');
    var $progressbar = $BODY.children('.ps-progress-body');
    
    function update(total, current) {
        total = PsIs.number(total) ? total : strToInt(total);
        current = PsIs.number(current) ? current : strToInt(current);
        
        var value = PsMath.round(current*100/total, 2);
        $pct.html(value);
        $total.html(total);
        $current.html(current);
        return $progressbar.progressbar({
            value: value
        });
    }
    
    total = isDefined(total) ? total : $total.htmlEnsureIsNumber();
    current = isDefined(current) ? current : $current.htmlEnsureIsNumber();
    
    return update(total, current);
}


//datepicker
jQuery.fn.uiDatepickerDisable = function() {
    return this.datepicker("disable");
}

jQuery.fn.uiDatepickerEnable = function() {
    return this.datepicker("enable");
}

jQuery.fn.uiDatepickerIsEnabled = function() {
    return !this.datepicker("option", "disabled");
}

jQuery.fn.uiDatepickerSetEnabled = function(enabled) {
    return enabled ? this.uiDatepickerEnable() : this.uiDatepickerDisable();
}

var PsResources = {
    logger: PsLogger.inst('PsResources').setTrace(),
    
    callsCnt: 0, /*Кол-во вызовов метода*/
    
    cache: new ObjectsStore(),
    waits: new ObjectsStore(),
    
    /*
    Загружает размеры картинок
    
    PsResources.getImgSize('/resources/images/author2.jpg', function(wh, url) {
        alert(wh);
    });
     */
    getImgSize: function(url, callback, ctxt) {
        callback = PsUtil.once(callback, ctxt);
        
        if (isEmpty(url)) {
            callback(null, url);
            return;//---
        }
        
        var logger = this.logger;
        var cache = this.cache;
        var waits = this.waits;
        var request = ++this.callsCnt + '.';
        
        logger.logDebug('{} Загружаем размер картинки [{}]. Ожидающих: {}', request, url, waits.get(url, []).length);
        
        waits.putToArray(url, callback);
        
        var notify = function() {
            var has = cache.has(url);
            if (has) {
                var wh = cache.get(url);
                wh = wh ? {
                    w: wh[0],
                    h: wh[1],
                    toString: function(){
                        return 'w: '+this.w+', h: '+this.h
                    }
                } : null;
                
                waits.doIfHas(url, function(callbacks) {
                    logger.logDebug('{} Оповещаем функции обратного вызова ({}) для [{}], wh: [{}]', request, callbacks.length, url, wh);
                    callbacks.walk(function(callback) {
                        //Функция может и не быть передана, например для предзагрузки
                        callback(wh, url);
                    });
                });
                waits.remove(url);
            }
            return has;
        }
        
        //Проверим, может картинка уже загружена или сейчас загружается
        if (notify()) {
            return; //Картинка была ранее загружена
        }
        if (waits.get(url, []).length > 1) {
            logger.logTrace('{} Картинка уже загружается, вызов отложен', request, url);
            return;//---
        }
        
        //Данный ресурс никто ранее не запрашивал и никто ранее не запрашивал, запросим мы.
        logger.logTrace('{} Посылаем запрос', request, url);
        
        //Выполним в отложенном режиме, чтобы, если картинка добавлена на страницу
        PsUtil.scheduleDeferred(function() {
            var IMG = new Image();
            IMG.onload = function () {
                cache.set(url, [IMG.width, IMG.height]);
                notify();
            }
            IMG.onerror = function() {
                cache.set(url, null);
                notify();
            }
            IMG.src = url;
        });
    
    },
    
    isImgLoaded: function(src) {
        return this.cache.has(src);
    },
    
    setImgLoaded: function(src, w, h) {
        this.cache.set(src, PsIs.number(w) && w>0 && PsIs.number(h) && h>0 ? [w, h] : null);
    },
    
    //Проверяет, существует ли ресурс. 
    //callback(exists, result)
    isFileExists: function(url, callback) {
        $.ajax({
            url: url,
            success: function(res) {
                callback(true, res);
            },
            error: function(err) {
                callback(false, err);
            }
        });
    },
    
    //PsResources.include(PsFoldingManager.POPUP('timeline').path('lines/line.js'));
    include: function(url) {
        var ext = getStringEnd(url, '.', false);
        if(!ext) return;//---
        switch (ext) {
            case 'js':
                this.includeJs(url);
                break;
            case 'css':
                this.includeCss(url);
                break;
        }
    },
    
    includeJs: function(url) {
        var $tag = $('script[src="'+url+'"]');
        if(!$tag.isEmptySet()) {
            this.logger.logInfo('javascript [{}] уже подключен, пропускаем.', url);
            return;//---
        }
        this.logger.logInfo('Подключаем javascript: [{}]', url);
        $('<script>').attr({
            type: 'text/javascript',
            src: url
        }).appendTo('head');
    },
    
    
    includeCss: function(url) {
        var $tag = $('link[href="'+url+'"]');
        if(!$tag.isEmptySet()) {
            this.logger.logInfo('css [{}] уже подключен, пропускаем.', url);
            return;//---
        }
        this.logger.logInfo('Подключаем css: [{}]', url);
        $('<link>').attr({
            rel: 'stylesheet',
            type: 'text/css',
            href: url
        }).appendTo('head');
    }
}


var PsJquery = {
    logger: PsLogger.inst('PsJquery').setDebug()/*.disable()*/,
    loggerOnLoad: PsLogger.inst('PsJquery.onLoad').setDebug()/*.disable()*/,
    
    on: function(options) {
        options = $.extend({
            ctxt: null,
            parent: 'body',
            item: null,
            data: null,
            mouseenter: null,
            mousemove: null,
            mouseleave: null
        }, options);
        
        
        var evtMap = {};
        
        function register(v) {
            evtMap[v] = function(e) {
                options[v].call(options.ctxt, e, $(this), options.data);
            }
        }
        
        for (var v in options) {
            if (PsArrays.inArray(v, ['ctxt', 'parent', 'item', 'data'])) continue;
            if ($.isFunction(options[v])) {
                //Замыкания
                register(v);
            }
        }
        
        if (!PsObjects.hasKeys(evtMap)) {
            return;//---
        }
        
        /*
         * В этом месте нужно быть уверенным, что страница загрузилась, так как
         * привязка jQuery.on(...) может не сработать, если на момент привязки не будет 
         * существовать родительский элемент, например body.
         */
        $(function() {
            if (PsIs.string(options.item)) {
                $(options.parent).on(evtMap, options.item);
            }
            
            if (PsIs.jQuery(options.item)) {
                $(options.item).on(evtMap);
            }
        });
    },
    
    /*
     * Функция будет выполнена в тот момент, когда элемент станет виден.
     * Метод будет вызван единожды!
     */
    executeOnElVisible: function(selector, callback, ctxt) {
        if(!$.isFunction(callback)) return;//---
        if(PsIs.empty(selector)) return;//---
        
        var isjQuery = PsIs.jQuery(selector);
        var isSelector = PsIs.string(selector);
        
        if(!isjQuery && !isSelector) return;//---
        
        /*
         * Основной метод, проверяющий видимость элемента и выполняющий callback.
         * Флаг canUseLiveQuery говорит и том, что если див сейчас не виден, то мы можем присвоеть ему id
         * и следить через livequery за его видимостью. Это нужно только в том случае, когда нам передан 
         * набор элементов jQuery (чтобы мы не потеряли элементы из всего набора, которые в данный момент не видимы).
         */
        function doProcess($div, canUseLiveQuery) {
            //private - функция, проверяющая, был ли див ранее обработан
            function wasProcessed() {
                return PsIs.array($div.data('on-show-callbacks')) && $div.data('on-show-callbacks').contains(callback);
            }
            
            if (wasProcessed()) {
                return true;//---
            }
            
            //private - функция, помечающая див, как обработанный
            function markProcessed() {
                if(!PsIs.array($div.data('on-show-callbacks'))) {
                    $div.data('on-show-callbacks', []);
                }
                $div.data('on-show-callbacks').push(callback);
            }
            
            if ($div.isVisible()) {
                markProcessed();
                callback.call(ctxt, $div);
                return true;//---
            }
            
            if (canUseLiveQuery) {
                var divId = $div.ensureIdIsSet('EL').attr('id');
                $('#'+divId+':visible').livequery(function() {
                    doProcess($div, false);
                });
            }
            
            return false;
        }
        
        var jobDone = false;
        //Пробегаем по всем элементам, независимо от того - передан нам набор jQuery или селектор
        $(selector).each(function() {
            jobDone = doProcess($(this), isjQuery);
        });
        
        if (isSelector) {
            if (selector.contains('#') && jobDone) {
                //Нам передан элемент с id, и при этом мы его уже обработали
                return;//---
            }
            $(selector+':visible').livequery(function() {
                doProcess($(this), false);
            });
        }
    },
    
    /**
     * Метод вызывается для оповещения слушателя о том, что все картинки,
     * находящиеся в блоке, были успешно загружены.
     */
    onLoadCnt: 0,
    onLoad: function(box, callback, ctxt) {
        //Сразу защитимся от повторного вызова
        callback = PsUtil.once(callback, ctxt);
        
        //Проверим картинки, которые могут быть уже загружены на момент их проверки
        var $box = $(box);
        var $imgs = $box.extractTarget('img:not(.x-ready)');
        var waits = 1 + $imgs.size();
        var notify = function() {
            if (--waits==0) callback($box);
        }
        var onLoad = function($img, ok) {
            $img.addClass('x-ready').toggleClass('x-error', !ok);
            notify();
        }
        
        $imgs.extractTarget('img:not(.x-ready)').each(function() {
            var $img = $(this);
            var src = $img.attr('src');
            if (src) {
                PsResources.getImgSize(src, function(wh) {
                    onLoad($img, wh);
                });
            } else {
                onLoad($img, false);
            }
        });
        
        notify();
    },
    
    /**
     * Метод позволяет добавить слушателя на клик по ссылке.
     * 
     * В качестве selector можно передать 'string selector' или 'jquery object'.
     * В зависимости от этого будет использован $.on(...) или $.click(...)
     * 
     * Вторым аргументом передаётся объект с параметрами обработки click.
     * Могут быть два варианта - клик с подтверждением и просто клик. 
     * Подтверждение будет показано, если передан options.msg - текст подтверждения
     * или функция обратного вызова передана в options.yes.
     * 
     * callback.call(ctxt, $href, anchor, data)
     */
    confirmTimer: {
        delay: 3000,
        timer: null,
        start: function(callback) {
            if (this.timer) this.timer.flush();
            this.timer = new PsTimerAdapter(callback, this.delay);
            this.timer.start();
        },
        stop: function() {
            if(!this.timer) return;//---
            this.timer.stop();
            this.timer = null;
        }
    },
    onHrefClick: function(selector, options) {
        if(PsIs.empty(selector) || (!PsIs.object(options) && !$.isFunction(options))) return;//---
        
        var isJquery = PsIs.jQuery(selector);
        
        if (isJquery && !selector.is('a')) return;//---
        if(!isJquery && !PsIs.string(selector)) return;//---
        
        var settings = $.isFunction(options) ? {
            clbk: options
        } : options;
        
        options = $.extend({
            ctxt: null,  //Контекст вызова обратной функции
            data: null,  //Данные, которые будут переданы в функцию обратного вызова
            msg: null,   //Текст подтверждения выполнения действия, например 'Вы уверены?'
            yes: null,   //Функция, вызываемая для подтверждения выполнения действия
            clbk: null   //Функция обратного вызова - в случае, когда вызов идёт без подтверждения
        }, settings);
        
        var hasMsg = options.msg!==null;
        var hasYes = $.isFunction(options.yes);
        var hasClbk = $.isFunction(options.clbk);
        
        if(!hasYes && !hasClbk) return;//---
        
        var isConfirmable = hasMsg || hasYes;
        
        //Функция, которая выполняющая фактический клик по ссылке
        var onClickImpl = function(e, $href) {
            e.preventDefault();
            (hasYes ? options.yes : options.clbk).call(options.ctxt, $href, getHrefAnchor($href), options.data);
        }
        
        //В случае, если мы работаем с подтверждением - 
        var TIMER = this.confirmTimer;
        
        var onClick = function(e, $href) {
            if (!isConfirmable) {
                onClickImpl(e, $href);
                return;//---
            }
            
            var $ok = crA().append('Да').addClass('yes');
            var $cancel = crA().append('Нет');
            
            var $dialog = $('<span>').addClass('confirm')
            .append(options.msg)
            .append(options.msg ? '&nbsp;&nbsp;' : '')
            .append($ok)
            .append('&nbsp;|&nbsp;')
            .append($cancel)
            .disableSelection();
            
            var doCancel = PsUtil.once(function() {
                TIMER.stop();
                $dialog.remove();
                $href.show();
                return false;
            });
            
            // Create ok button, and bind in to a click handler.
            $ok.click(function() {
                doCancel();
                onClickImpl(e, $href);
                return false;
            })
            
            $cancel.click(doCancel);
            
            $dialog.insertBefore($href.hide());
            
            TIMER.start(doCancel);
        }
        
        if (isJquery) {
            selector.click(function(e) {
                onClick(e, $(this));
            });
        } else {
            PsJquery.on({
                item: selector,
                click: onClick
            });
        }
    }
}

/*
 * Помощник в добавлении слушателей на прокрутку колёсика.
 * Добавляйте его самостоятельно при наведении на необходимый элемент и отключайте при выведении курсора с него.
 * http://markup-javascript.com/2009/05/24/krossbrauzernyj-mousewheel-obrabotka-sobytiya-skrolinga/
 * http://www.adomas.org/javascript-mouse-wheel/
 */
var PsMouseWheelHelper = {
    //Слушатели прокрутки колёсика мыши
    listeners: [],
    
    //Призанк прерывания базового события. Если хоть один слушатель требует его,
    //то событие будет прервано и слушатели, не требуещие прерывания прокрутки,
    //просто не получат соответствующего события.
    prevent: false, 
    
    //Пересчёт признака prevent
    _recalcPrevent: function() {
        this.prevent = false;
        this.listeners.walk(function(ob) {
            this.prevent = this.prevent || ob.prevent;
        }, false, this);
    },
    
    //Кажды callback+prevent оборачиваются в объект. Компаратор нужен, чтобы их находить.
    _comparator: function(callback, ob) {
        return callback === ob.callback;
    },
    
    addListener: function(callback, prevent, ctxt) {
        if (this.listeners.contains(callback, this._comparator)) return;//---
        this.listeners.push({
            ctxt: ctxt,
            callback: callback,
            prevent: !!prevent
        });
        this._recalcPrevent();
        this._appendListener();
    },
    
    removeListener: function(callback) {
        this.listeners.removeValue(callback, this._comparator);
        this._recalcPrevent();
    },
    
    _appended: false,
    _appendListener: function() {
        if (this._appended) return; //---
        this._appended = true;
        var self = this;
        // Основная Функция mousewheel
        var onWheel = function(event) {
            if(!self.listeners.length) return; /*Если нет слушателей - выходим*/
            
            var delta = 0;
            if (!event) event = window.event; /*Событие IE.*/
            // Установим кроссбраузерную delta
            if (event.wheelDelta) {
                // IE, Opera, safari, chrome - кратность дельта равна 120
                delta = event.wheelDelta/120;
            }
            else if (event.detail) {
                // Mozilla, кратность дельта равна 3
                delta = -event.detail/3;
            }
            
            if(!delta) return; //---
            
            // Пробегаем по всем слушателям, вызывая их.
            // Если мы должны прерывать скроллинг, то это событие получат только те стушатели, которые также ожидают прерывания базового события.
            self.listeners.walk(function(ob) {
                if (!self.prevent || ob.prevent) {
                    ob.callback.call(ob.ctxt, delta);
                }
            });
            
            if (self.prevent) {
                // Отменим текущее событие - событие поумолчанию (скролинг окна).
                if (event.preventDefault)
                    event.preventDefault();
                event.returnValue = false; /* для IE */
            }
        }
        
        // Инициализация события mousewheel
        if (window.addEventListener) /*mozilla, safari, chrome*/
            window.addEventListener('DOMMouseScroll', onWheel, false);
        // IE, Opera.
        window.onmousewheel = document.onmousewheel = onWheel;
    }
}

/*
 * Класс позволяет повесить слушатель на событие перезагрузки страницы.
 * Причём метод будет вызван ДО начала загрузки новой страницы, а не после этого.
 * Если метод вернёт строку, то она будет показана пользователю в качестве подтверждения
 * намерений о закрытии страницы.
 * 
 * Внимание!!!
 * Не работает в opera
 */

var PsUnloadListener = {
    added: false,
    listeners: [],
    logger: PsLogger.inst('PsUnloadListener').setDebug()/*.disable()*/,
    addListener: function(fn, ctxt) {
        this.logger.logInfo('Добавляем новый слушатель перезагрузки окна');
        
        if(!$.isFunction(fn)){
            this.logger.logError('Передана не функция, пропускаем');
            return;//---
        }
        
        if (this.listeners.contains(fn)){
            this.logger.logInfo('Данный слушатель уже зарегистрирован, пропускаем');
            return;//---
        }
        
        this.listeners.push([fn, ctxt ? ctxt : null]);
        
        if (this.added) return; //---
        this.added = true;
        
        this.logger.logInfo('Добавляем слушатель на событие "beforeunload"');
        
        var _this = this;
        $(window).bind('beforeunload', function(){
            var waitText = null;
            
            _this.listeners.walk(function(fn) {
                var _text = fn[0].call(fn[1]);
                if (PsIs.string(_text) && !PsIs.empty(_text)){
                    waitText = _text;
                }
            });
            
            _this.listeners = [];
            
            if (waitText) {
                return waitText;
            }
        //Мы вынуждены ничего не вернуть, так как в противном случае будет показано подтверждение
        });
    },
    
    /*
     * До перезагрузки страницы будут вызваны все зарегистрированные функции.
     * Возвращённые ими объекты будут смёрджены и их свойства отправлены на сервер,
     * чтобы они могли быть учтены при построении страницы.
     */
    propsListeners: [],
    addProp4ServerSend: function(fn, ctxt) {
        this.logger.logInfo('Регистрируем провайдера свойств');
        
        if(!$.isFunction(fn)){
            this.logger.logError('Передана не функция, пропускаем');
            return;//---
        }
        
        if (this.propsListeners.contains(fn)){
            this.logger.logInfo('Данный провайдер уже зарегистрирован, пропускаем');
            return;//---
        }
        
        this.propsListeners.push([fn, ctxt ? ctxt : null]);
        
        this.addListener(this.doSendProps, this);
    },
    
    doSendProps: function() {
        var send = {};
        
        this.propsListeners.walk(function(fn) {
            var ob = fn[0].call(fn[1]) || {};
            send = $.extend(send, ob);
        });
        
        this.logger.logInfo('Отправляем на сервер ключи: [{}]', PsObjects.keys2array(send));
        
        $.ajax({
            type: "GET",
            url: "ajax/BeforePageUnload.php",
            async: false,
            data: send
        });
    }
}


/*
 * Горячие клавиши
 */
var PsHotKeysManager = {
    listeners: {},
    logger: PsLogger.inst('PsHotKeysManager').setDebug()/*.disable()*/,
    hasListener: function(hotKey) {
        return this.listeners.hasOwnProperty(hotKey);
    },
    getInfo: function() {
        var res = [];
        PsObjects.keys2array(this.listeners).walk(function(HotKey) {
            this.listeners[HotKey].walk(function(o) {
                if (o.descr) {
                    res.push({
                        key: HotKey,
                        descr: o.descr
                    });
                }
            });
        }, false, this);
        return res;
    },
    addListener: function(hotKey, options) {
        options = $.extend({
            f: function(HotKey) {
                InfoBox.popupError('Обработчик для $ не реализован'.replace('$', HotKey));
            },
            ctxt: null,
            descr: null,
            enableInInput: false, /*Срабатывать ли в полях ввода*/
            stopPropagate: false  /*Прерывать ли событие*/
        }, options);
        
        
        this.logger.logInfo('Добавляем новую горячую клавишу на комбинацию [{}] "{}"', 
            hotKey, options.descr ? options.descr : 'нет описания');
        
        if (PsIs.empty(hotKey)) {
            this.logger.logError('Передана пустая комбинация, пропускаем');
            return;//---
        }
        
        if(!$.isFunction(options.f)) {
            this.logger.logError('Передана не функция, пропускаем');
            return;//---
        }
        
        var hasPrev = this.hasListener(hotKey);
        
        if(!hasPrev) {
            this.listeners[hotKey] = [];
        }
        
        if (this.listeners[hotKey].contains(options, function(o1, o2) {
            return o1.f === o2.f;
        })) {
            this.logger.logInfo('Данный слушатель уже зарегистрирован, пропускаем');
            return;//---
        }
        
        this.listeners[hotKey].push(options);
        
        if (hasPrev) return;//---
        
        $(function() {
            /*
             * Регистрацию горачей клавиши проведём после того, как окно будет готово.
             * Можно зарегистрироваться и сразу, но сделаем это позже, чтобы внешнему коду 
             * не нужно было думать о загрузке окна.
             */
            shortcut.add(hotKey, function() {
                PsHotKeysManager.process.call(PsHotKeysManager, hotKey);
            }, 
            {
                disable_in_input: !options.enableInInput, 
                propagate: !options.stopPropagate
            });
        });
    },
    
    process: function(hotKey) {
        //На всякий случай нужно проверить, есть ли слушатель, так как он может быть вызвана напрямую
        if (this.hasListener(hotKey)) {
            this.logger.logDebug('Нажата комбинация [{}]', hotKey);
            this.listeners[hotKey].walk(function(options) {
                options.f.call(options.ctxt, hotKey);
            });
        }
    }
}


/* Метод возвращает различные готовые объекты для встраивания в html */
var PsHtml = {
    span: function(text, _class) {
        return '<span class="'+$.trim(_class)+'">'+text+'</span>';
    },
    
    span$: function(text, _class) {
        return $('<span>').addClass(_class).html(text);
    },
    
    div: function(text, _class) {
        return '<div class="'+_class+'">'+text+'</div>';
    },
    
    div$: function(text, _class) {
        return $('<div>').addClass(_class).html(text);
    },
    
    mathText: function(symbol) {
        return this.span(symbol, 'math_text');
    },
    
    num2str: function(num) {
        return PsIs.number(num) && num<0 ? '&minus;' + Math.abs(num) : '' + num;
    },
    
    //Возвращает число со знаком. Если это -1, то вернётся 'минус', если 0 или 1 - 'ничего'
    num2strTrim: function(num) {
        var abs = Math.abs(num);
        return (PsIs.number(num) && num<0 ? '&minus;' : '') + (abs==0 || abs==1 ? '' : abs);
    },
    
    //Если число отрицательное - оборачивает его в скобки. Нужно для умножения
    num2strBr: function(num) {
        return num<0 ? '('+this.num2str(num)+')' : num;
    },
    
    sum2str: function(x1, x2) {
        var res = this.num2str(x1);
        switch (PsMath.sign(x2)) {
            case 1:
                return res + ' + ' + Math.abs(x2);
            case -1:
                return res + ' &minus; ' + Math.abs(x2);
        }
        return res;
    },
    
    vector: function(val, x, y) {
        val = val.contains('math_vector') ? val : this.span(val, 'math_vector');
        return val + (isDefined(x) && isDefined(y) ? ' ' + this.coords(x, y) : '');
    },
    
    vecSum: function(x, v1, y, v2) {
        var vec1 = '';
        var sign = '';
        var vec2 = '';
        
        var ax = Math.abs(x);
        var ay = Math.abs(y);
        
        if (x!=0) {
            vec1 = (x<0 ? '&minus;' : '') + (ax==1 ? '' : ax) + this.vector(v1);
        }
        
        if (y!=0) {
            sign = y<0 ? ' &minus;' : ' +';
            vec2 = (ay==1 ? '' : ay) + this.vector(v2);
        }
        
        var res = $.trim(vec1 + (vec1 || y<0 ? sign : '') + (vec1 ? ' ' : '') + vec2);
        return res ? res : this.vector('0');
    },
    
    coords: function(x, y) {
        return '('+this.num2str(x)+', '+this.num2str(y)+')';
    },
    
    binom: function (n, k) {
        return '<span class="binon_holder">&nbsp;<table class="binom"><tbody><tr><td class="lbr" rowspan="2"></td><td>'+n+'</td><td class="rbr" rowspan="2"></td></tr><tr><td>'+k+'</td></tr></tbody></table></span>';
    },
    
    combination: function(n, k) {
        return 'C<sub>' + n + '</sub><sup>' + k + '</sup>';
    },
    
    //распорка (для того, чтобы умещались в одной строке поля с интексами и поле не скакало)
    strut: function() {
        return '<span class="invisible"><sup>1</sup><sub>1</sub></span>';
    },
    
    //Блок скрытого текста - аналогичен {hidden}
    hiddenBox$: function(title, content, isToggle) {
        return (isToggle ? $('<div>') : $('<span>')).addClass('ps-hidden-box').append(content).data('title', title);
    }
}

var PsHtmlCnst = {
    ALPHA: PsHtml.mathText('&alpha;'),
    BETA: PsHtml.mathText('&beta;'),
    GAMMA: PsHtml.mathText('&gamma;'),
    PI: PsHtml.mathText('&pi;'),
    MU: PsHtml.mathText('&mu;'),
    LAMBDA: PsHtml.mathText('&lambda;'),
    INT: PsHtml.mathText('&int;'),
    
    VECT_A: PsHtml.vector('a'),
    VECT_B: PsHtml.vector('b'),
    VECT_C: PsHtml.vector('c'),
    VECT_I: PsHtml.vector('i'),
    VECT_J: PsHtml.vector('j'),
    VECT_E1: PsHtml.vector('e<sub>1</sub>'),
    VECT_E2: PsHtml.vector('e<sub>2</sub>'),
    
    X0: 'x<sub>0</sub>',
    X1: 'x<sub>1</sub>',
    X2: 'x<sub>2</sub>'
}

/*
PsUtil.startTimerOnce(function(){
    var um = new PsUpdateModel(null, function(action) {
        InfoBox.popupInfo('On start: ' + action);
    }, function(action){
        InfoBox.popupInfo('On stop: ' + action);
    });
    um.start('123');
    um.start('456');
    um.stop();
    um.stopDeferred();
}, 5000);
 */

/*
 * --== КОНТРОЛЛЕРЫ ==--
 * Классы, создаваемые для управления какими-либо элементами страницы
 */

/*
 * Менеджер по управлению красивыми кнопками (как под задачами с шахматами)
 * По умолчанию - считаем, что все кнопки включены
 */
function ButtonsController($buttons, _params) {
    $buttons = $buttons.extractTarget('button.imaged, a.imaged');
    var PARAMS = $.extend({
        id: null,
        ctxt: null,
        click: function(action) {
            InfoBox.popupError('Callback not implemented for action [' + action + ']');
        }
    }, _params||{});
    
    var BUTTONS = {};
    
    $buttons.each(function() {
        var $button = $(this);
        var action = $button.data('action');
        
        if (isEmpty(action)) return;//---
        
        var type = $button.data('type');
        var isTrigger = type == 'trigger';
        var isHref = $button.is('a');
        var storeKey = PARAMS.id ? PARAMS.id + '_' + action : null;
        
        //Может быть несколько кнопок с одним action (например ссылки)
        if (BUTTONS[action]) {
            BUTTONS[action].btn = BUTTONS[action].btn.add($button);
        }
        else
        {
            BUTTONS[action] = {
                btn: $button,
                type: type
            }
        }
        
        if (isTrigger) {
            $button.click(function() {
                var isOn = !$button.toggleClass('gray').is('.gray');
                if (storeKey) {
                    PsLocalStore.COMMON.set(storeKey, isOn);
                }
                
                var ownF = $.isFunction(PARAMS['on_'+action]) ? PARAMS['on_'+action] : null;
                if (ownF) {
                    ownF.call(PARAMS.ctxt, isOn);
                }else{
                    PARAMS.click.call(PARAMS.ctxt, action, isOn);
                }
            }).addClass('gray');
            
            if (storeKey && PsLocalStore.COMMON.get(storeKey, false)) {
                $button.click();
            }
        }else
        if(isHref) {
            var href = $button.attr('href');
            $button.clickClbck(function() {
                var ownF = $.isFunction(PARAMS['on_'+action]) ? PARAMS['on_'+action] : null;
                if(ownF) {
                    ownF.call(PARAMS.ctxt, href, $button);
                }else{
                    PARAMS.click.call(PARAMS.ctxt, action, href, $button);
                }
            });
        }else{
            $button.click(function() {
                var ownF = $.isFunction(PARAMS['on_'+action]) ? PARAMS['on_'+action] : null;
                if (ownF) {
                    ownF.call(PARAMS.ctxt, $button);
                } else {
                    PARAMS.click.call(PARAMS.ctxt, action, $button);
                }
            });
        }
    });
    
    this.setCallbacks = function(_params) {
        $.extend(PARAMS, _params);
        return this;
    }
    
    this.recalcState = function(states) {
        $.each(states, function(name, state) {
            if(!BUTTONS[name]){
                return;
            }
            state = $.extend({
                visible: 1,
                colored: 1,
                enabled: 1
            }, state);
            
            var $btn = BUTTONS[name].btn;
            var type = BUTTONS[name].type;
            
            if (type != 'trigger') {
                $btn.toggleClass('gray', !state.colored);
            }
            $btn.setVisibleInline(state.visible);
            $btn.setEnabled(state.enabled);
        });
        return this;
    }
    
    this.setBtnVisible = function(name, isVisible) {
        if(!BUTTONS[name]) return;
        BUTTONS[name].btn.setVisibleInline(isVisible);
    }
    
    this.getBtn = function(action) {
        return this.buttons.hasOwnProperty(action) ? this.buttons[action].btn : null;
    }
    
    return this;
}

/*ImageButtonsStatesController*/
var IBStatesController = function($button, options) {
    $button = $button.extractTarget('button.imaged.states');
    if ($button.size()!=1) {
        /*todo Ошибка!*/
        return;
    }
    
    var STATE = null;
    var STATES = [];
    var $IMAGES = {};
    
    var _this = this;
    
    $button.children('img').each(function() {
        var $img = $(this);
        var state = extractFileName($img.attr('src'));
        STATES.push(state);
        $IMAGES[state] = $img;
    });
    
    this.setState = function(state) {
        for (var v in $IMAGES) {
            $IMAGES[v].setVisible(state==v);
        }
        STATE = state;
    }
    
    this.setNextState = function() {
        this.setState(PsArrays.nextOrFirstItem(STATES, STATE));
    }
    
    this.resetState = function() {
        this.setState(STATES[0]);
    }
    
    this.show = function() {
        $button.show();
    }
    
    this.hide = function() {
        $button.hide();
    }
    
    this.setVisible = function(vis) {
        $button.setVisible(vis);
    }
    
    $button.click(function() {
        if (!STATE) return;
        
        if ($.isFunction(options['on_' + STATE])) {
            options['on_' + STATE].call(_this);
        }
        if ($.isFunction(options.click)) {
            options.click.call(_this);
        }        
    });
}


var ButtonsControllerTools = {
    progressStart: function($btn) {
        if($btn.is('a')) {
            $btn.hide();
            var $span = $('<span>').addClass('imaged').append(crIMG('/resources/images/icons/controls/loading.gif'));
            $span.insertAfter($btn);
        }
    },
    
    progressStop: function($btn) {
        if($btn.is('a')) {
            $btn.next('span.imaged').remove();
            $btn.show();
        }
    }
}

function PsInfoBoxController($BOX) {
    $BOX.hide();
    
    var timer = null;
    
    var clear = function() {
        if (timer) {
            timer.stop();
            timer = null;
        }
        /*
         * stop(true, false) - второй параметр отменяет вызов функции после окончания анимации 
         * (если таковая сейчас идёт). Нам не надо повторно вызывать clear().
         * 
         * opacity=1 устанавливаем, т.к. блок остаётся в том состоянии, в котором прекратилась 
         * анимация.
         */
        $BOX.stop(true, false).hide().empty().css('opacity', '1');
    }
    
    var show = function(content, avtoHide) {
        clear();
        $BOX.append(content).show();
        
        if (!avtoHide) return;
        
        timer = new PsTimerAdapter(function() {
            $BOX.fadeOut('slow', clear);
        }, 2000);
        timer.start();
    }
    
    this.clear = clear;
    
    this.info = function(content, avtoHide) {
        show(content, avtoHide);
    };
    
    this.success = function(content, avtoHide) {
        show(span_success(content), avtoHide);
    };
    
    this.error = function(content, avtoHide) {
        show(span_error(content), avtoHide);
    };
    
    this.progress = function(content){
        show(span_progress(content), false);
    };
}

/*
 * Менеджер по управлению информационными блоками
 * todo - выкинуть
 */
function infoBlockController(block) {
    var _this = this;
    
    this.block = $(block).hide();
    
    this.timer = null,
    
    this.clear = function(){
        if(this.timer){
            clearTimeout(this.timer);
            this.timer = null;
        }
        this.block.empty().hide();
    };
    
    var showInfoImpl = function(content, avtoHide){
        _this.clear();
        
        /*
         * stop(true, false) - второй параметр отменяет вызов функции после окончания анимации 
         * (если таковая сейчас идёт). Нам не надо повторно вызывать clear().
         * 
         * opacity=1 устанавливаем, т.к. блок остаётся в том состоянии, в котором прекратилась 
         * анимация.
         */
        var infoDiv = _this.block.stop(true, false).empty().append(content).css('opacity', '1').show();
        
        if(avtoHide){
            _this.timer = setTimeout(function() {
                infoDiv.fadeOut('slow', function(){
                    _this.clear();
                });
            }, 2000);
        }
    };
    
    this.info = function(content){
        showInfoImpl(content, false);
    };
    
    this.infoAH = function(content){
        showInfoImpl(content, true);
    };
    
    this.success = function(content){
        showInfoImpl(span_success(content), false);
    };
    
    this.successAH = function(content){
        showInfoImpl(span_success(content), true);
    };
    
    this.error = function(content){
        showInfoImpl(span_error(content), false);
    };
    
    this.errorAH = function(content){
        showInfoImpl(span_error(content), true);
    };
    
    this.progress = function(content){
        showInfoImpl(span_progress(content), false);
    };
}

/*
 * Утилиты
 */
function isDefined(value) {
    return typeof (value) != 'undefined';
}

function isEmpty(value){
    return !isDefined(value) || ($.isArray(value) && value.length==0) || (isString(value) ? jQuery.trim(value).length==0 : (value instanceof jQuery ? value.size()==0 : !value));
}
// Test of [isEmpty] function
/*
$.each(['a', null, 0, '0', false, null, {}], function(id, val){
    alert('value: ' + val + ', type: ' + typeof(val) + ', empty: ' + isEmpty(val));
});
 */

function elIdFull(element){
    var id = (!element || typeof(element)=='string') ? element : ((element instanceof jQuery) ? element.attr('id') : element.id);
    return isEmpty(id) ? null : id;
}

function elId(elem){
    var id = elIdFull(elem);
    id = isEmpty(id) ? null : id.split('_');
    return id ? id[id.length-1] : null;
}

function elGr(elem){
    var id = elIdFull(elem);
    id = isEmpty(id) ? null : id.split('_');
    return id ? id[id.length-2] : null;
}

function isString(obj){
    return obj!=null && typeof(obj)=='string';
}

function strToInt(num, def) {
    num = $.trim(num).replace('−', '-');
    return PsIs.number(num) ? 1*num : (PsIs.number(def) ? 1*def : null);
}

function crIMG(src, alt){
    return $('<img />').attr('src', src).attr('alt', alt ? alt : '');
}

function crA(href, title){
    var $a = $('<a />').attr('href', href ? href : '#');
    if (title) {
        $a.attr('title', title);
    }
    return $a;
}

function crCloser(callback, addClass) {
    return crA().html('X').addClass('closer').addClass(addClass).clickClbck(callback);
}

/*
 * Позиционирует экран на элементе с заданным ID
 */
function jumpToElement(id) {
    var el;
    if (PsIs.jQuery(id)) {
        el = id;
    }
    else if(isString(id)) {
        id = id.startsWith('#') ? id : '#'+id;
        el = $(id);
    }
    
    if(!isEmpty(el)){
        var new_position = el.offset();
        window.scrollTo(new_position.left, new_position.top);
    }
}


//TODO - все скролы вынести на PsScroll
function scrollTop(){
    jQuery.scrollTo(0, 500);
}

var PsScroll = {
    isScrolling: function() {
        return $('html:animated').size() > 0;
    },
    scrollTop: function(speed) {
        jQuery.scrollTo(0, PsIs.number(speed) || speed ? speed : 500);
    },
    jumpTop: function() {
        jQuery.scrollTo(0);
    },
    scrollBottom: function(speed) {
        jQuery.scrollTo($(document).height(), PsIs.number(speed) || speed ? speed : 500);
    },
    jumpBottom: function() {
        this.scrollBottom(0);
    },
    jumpTo: function(el) {
        if (!el) return;//---
        el = $(PsIs.string(el) ? el.ensureStartsWith('#') : el);
        
        if(!isEmpty(el)){
            var new_position = el.offset();
            window.scrollTo(new_position.left, new_position.top);
        }
    },
    scrollTo: function(el, speed, onAfter, ctxt) {
        if (!el) return;//---
        el = $(PsIs.string(el) ? el.ensureStartsWith('#') : el);
        if (isEmpty(el)) return;//---
        
        jQuery.scrollTo(el.offset().top, PsIs.number(speed) || speed ? speed : 500, {
            onAfter: PsUtil.once(onAfter, ctxt)
        });
    }
}

function loadingMessageDiv(message){
    return jQuery('<div>').addClass('pageLoading').
    append(isEmpty(message) ? '' :  jQuery('<h1>').html(jQuery.trim(message))).
    append(crIMG(CONST.IMG_LOADING_LONG, 'page loading'));
}

function clearDiv(){
    return jQuery('<div>').addClass('clearall');
}

function noItemsDiv(text){
    text = text ? text : 'Нет элементов для отображения';
    return jQuery('<div>').addClass('no_items').html(text);
}

function getStringStart(str, firstChar, strIfNotContains){
    var idx = str ? str.indexOf(firstChar) : -1;
    return idx < 0 ? (strIfNotContains ? str : null) : str.substr(0, idx);
}

function getStringEnd(str, lastChar, strIfNotContains){
    var lastIdx = str ? str.lastIndexOf(lastChar) : -1;
    if (lastIdx >= 0 && lastChar.length>1){
        lastIdx +=  lastChar.length-1;
    }
    return lastIdx < 0 ? (strIfNotContains ? str : null) : str.substr(lastIdx+1, str.length);
}

function getHrefAnchor(a){
    return $.trim(getStringEnd($(a).attr('href'), '#', false));
}

//lesson.php?post_id=6#p3
function getLocationUrl(href){
    href = href ? $(href).attr('href') : window.location.href;
    return getStringEnd(href, '/', true);
}

//lesson.php?post_id=6
function getLocationUrlNoHash(href){
    return getStringStart(getLocationUrl(href), '#', true);
}

function getUrlParamValue(href, paramName){
    href = (href instanceof jQuery) ? href.attr('href'): href;
    href = getStringEnd(href, paramName + '=', false);
    href = href ? getStringStart(href, '&', true) : href;
    return href;
}

// a/b/c/file.txt -> file
function extractFileName(src) {
    return getStringStart(getStringEnd(src.replace('\\', '/'), '/', true), '.', true);
}

function extractPostId(href){
    return getUrlParamValue(href, 'post_id');
}


function getSeconds(){
    return Math.round(new Date().getTime()/1000);
}

function isValidJSON(data){
    try
    {
        $.parseJSON(data);
        return true;
    }
    catch(err){
        return false;
    }
}

/*
$.ajaxSetup({
    //timeout: 5000,
    //type: 'POST',
    error: function(xhr) {
        InfoBox.popupError(ajax_error(xhr));
    }
});
 */
function span_success(text){
    return $('<span>').addClass('success').html(text ? text : '');
}

function span_error(text){
    return $('<span>').addClass('error').html(text ? text : '');
}

function span_blinking(text){
    return $('<span>').addClass('blinking').html(text ? text : '');
}

function span_progress(text){
    return $('<span>').addClass('progress').html(text ? text : '');
}

function span_ah($ctt, callback) {
    if(isString($ctt)){
        $ctt = $('<span>').html($ctt);
    }
    setTimeout(function() {
        $ctt.fadeOut('slow', function(){
            $ctt.remove();
            if($.isFunction(callback)){
                callback.call();
            }
        });
    }, 2000);
    
    return $ctt;
}



/* Менеджер, отображающий всплывающую подсказку в правон нижнем углу экрана.
 * 
 *             +++++++++|
 *             + popup +|
 *             +++++++++|
 * ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯
 */
var PopupManager = {
    inst: null,
    display: function(content, delay) {
        if (!this.inst) {
            //Для удобства всю имплементацию перенесём в отдельный класс
            
            //##PopupManagerImpl##
            function PopupManagerImpl() {
                var $window;
                
                var timer = new PsTimerAdapter(function() {
                    $window.slideUp('fast'); 
                }, 2000);
                
                this.showPopup = function(content, delay) {
                    timer.stop();
                    if($window) $window.remove();
                    
                    $window = $('<div>').
                    attr('id', 'popupWindow').hide().
                    appendTo('body').append(content).
                    slideDown('fast', function() {
                        timer.start(delay);
                    });
                }
                
                this.hidePopup = function() {
                    timer.flush();
                }
            }
            //##PopupManagerImpl##
            
            this.inst = new PopupManagerImpl();
        }
        this.inst.showPopup(content, delay);
        return content;
    },
    
    //Если окно сейас показано, то метод его спрячет
    hide: function() {
        if (this.inst) this.inst.hidePopup();
    }
}


/**
 * Расширение PopupManager для показа информационных сообщений.
 * 
 *             +++++++++++++++++|
 *             + info/warn/err +|
 *             +++++++++++++++++|
 * ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯
 */
var InfoBox = {
    __getBoxImpl: function(clas, text) {
        return $('<div>').addClass('info_box').addClass(clas).append(PsIs.jQuery(text) ? text : $.trim("" + text));
    },
    
    divInfo: function(text) {
        return this.__getBoxImpl('info', text);
    },
    
    divSuccess: function(text) {
        return this.__getBoxImpl('succ', text);
    },
    
    divError: function (text) {
        return this.__getBoxImpl('err', text);
    },
    
    divWarning: function (text) {
        return this.__getBoxImpl('warn', text);
    },
    
    divValidation: function (text) {
        return this.__getBoxImpl('validation', text);
    },
    
    /*Утилиты*/
    
    clearMessages: function($parent) {
        $parent.extractTarget('.info_box').remove();
    },
    
    fadeOut: function($box, secondsDelay) {
        PsUtil.startTimerOnce(function() {
            $box.fadeOut('slow');
        }, secondsDelay * 1000);
    },
    
    popupInfo: function(text, delay) {
        return PopupManager.display(this.divInfo(text), delay);
    },
    
    popupSuccess: function(text, delay) {
        return PopupManager.display(this.divSuccess(text), delay);
    },
    
    popupError: function(text, delay) {
        return PopupManager.display(this.divError(text), delay);
    },
    
    popupWarning: function(text, delay) {
        return PopupManager.display(this.divWarning(text), delay);
    },
    
    popupHide: function() {
        PopupManager.hide();
    }
}


function updateFormules(){
    if (window.MathJax){
        window.MathJax.Hub.Typeset();
    }
}


/*
 * Метод возвращает часть текста, выделенную пользователем.
 */

function copySelection(){ 
    if      (window.getSelection)   return window.getSelection().toString();      
    else if (document.getSelection) return document.getSelection();                
    else if (document.selection)   return document.selection.createRange().text;
    return null;
}

/*
 * ЛОКАЛЬНОЕ ХРАНИЛИЩЕ
 */

var PsLocalStore = {
    logger: PsLogger.inst('PsLocalStore').setDebug()/*.disable()*/,
    stores: {},
    readTimeout: 2000,
    isEnabled: isDefined(localStorage),
    scopeName: function(scope) {
        return PsIs.empty(scope) || !PsIs.string(scope) ? '_dflt_' : scope;
    },
    inst: function(scope) {
        scope = this.scopeName(scope);
        
        if(!this.isEnabled) {
            this.logger.logInfo('Запрошено хранилище [{}], возвращено пустое', scope);            
            this.stores[scope] = this.EmptyStore();
        }
        
        if(!this.stores[scope]) {
            this.logger.logInfo('Инициализируем хранилище [{}]', scope);            
            //Создадим хранилище, добавив метод put
            var store = new Store(scope, null, this.readTimeout);
            //Добавим метод put
            store.put = store.set;
            this.stores[scope] = store;
        }
        
        return this.stores[scope];
    },
    
    EmptyStore: function(){
        var EmptyStore = new ObjectsStore();
        for (var v in Store.prototype) {
            if($.isFunction(Store.prototype[v]) && !EmptyStore.hasOwnProperty(v)) {
                EmptyStore[v] = function(){};
            }
        }
        return EmptyStore;
    },
    
    //Добавляет свойства, которые будут отправлены на сервер
    serverSend: {},
    addProp4ServerSend: function(key, scope) {
        if(!this.isEnabled) return;//---
        
        scope = this.scopeName(scope);
        this.logger.logInfo('Свойство [{}] хранилища [{}] добавлено в список отправки на сервер', key, scope);
        
        if(!this.serverSend[scope]){
            this.serverSend[scope] = [];
        }
        this.serverSend[scope].push(key);
        
        PsUnloadListener.addProp4ServerSend(this.getProps4ServerSend, this);
    },
    
    
    /*
     * Метод будет вызван до перезагрузки страницы.
     * Мы сможем послать данные на сервер, чтобы они были учтены при построении страницы.
     */
    getProps4ServerSend: function() {
        var send = {};
        
        for(var scope in this.serverSend) {
            var store = this.inst(scope);
            var sendK = this.serverSend[scope];
            sendK.walk(function(key) {
                if (store.has(key)) {
                    send[key] = store.get(key);
                }
            })
        }
        
        return send;
    }
}

Store.prototype.addProp4ServerSend = function(key) {
    PsLocalStore.addProp4ServerSend(key, this.name);
}

//ЭКЗЕМПЛЯРЫ

PsLocalStore.COMMON = PsLocalStore.inst('common');
PsLocalStore.WIDGET = function(name) {
    return PsLocalStore.inst('widget_'+name);
}

/*
 * Javascript - ресурсы для фолдингов, позволяют:
 *  1. path  - позволяет получить путь к ресурсам от корня директории фолдинга
 *  2. src   - позволяет получить путь к папке src ресурсов от корня директории фолдинга
 *  3. store - локальное хранилище для данного фолдинга
 */
var PsFoldingManager = {
    store: new ObjectsStore(),
    FOLDING: function(funique, ident) {
        var eunique = funique+'-'+ident;
        var basePath = defs['foldings'][funique] + ident;
        if(!this.store.has(basePath)) {
            var panelJsParams = defs[CONST.PAGE_JS_GROUP_PANELS] || {};
            var obj = {
                path: function(path) {
                    return basePath + path.ensureStartsWith('/')
                },
                src: function(path) {
                    return this.path('src' + path.ensureStartsWith('/'));
                },
                store: function() {
                    return PsLocalStore.inst('folding-'+eunique);
                },
                //Данные для панели
                panel: function(panel) {
                    var panelJs = panelJsParams[funique+'-'+panel] || {};
                    return panelJs[ident];
                },
                //Логгер для данной сущности фолдинга
                logger: function() {
                    return PsLogger.inst(eunique);
                }
            }
            this.store.set(basePath, obj);
        }
        return this.store.get(basePath);
    }
}
