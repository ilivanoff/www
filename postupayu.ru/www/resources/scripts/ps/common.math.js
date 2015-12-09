var PsMathFuncDefInterval = {
    DELTA: 10, //Длина интервала проверки
    
    MAX_STORE_INTERVAL: 5,  //Максимальный номер интервала, на котором мы сохраняем расчёты
    MAX_STORE_INTERVALS: 5, //Максимальное кол-во интервалов на одном участке, которые мы сохраняем
    
    calls: 0,               //Номер вызова
    logger: PsLogger.inst('PsMathFuncDefInterval').setTrace(),
    store: PsLocalStore.inst('PsMathFuncDefInterval'),
    
    sign: ['DELTA', 'MAX_STORE_INTERVAL', 'MAX_STORE_INTERVALS'], //Поля, участвующие в подписи
    
    /**
     * Возвращает адаптер для работы с хранилищем для данного выражения ex и шага расчёта dx
     */
    getStoreAdapter: function(ex, dx) {
        return new function() {
            
            var SELF = PsMathFuncDefInterval;
            var STORE = PsMathFuncDefInterval.store;
            
            //Построим подпись кеша
            var selfSign = {
                DX:  dx,
                VER: PsMathEval.ver
            };
            //Добавляем к кешу ключи из SELF
            SELF.sign.walk(function(key) {
                assert(SELF.hasOwnProperty(key), 'Key ['+key+'] must present in PsMathFuncDefInterval');
                assert(!selfSign.hasOwnProperty(key), 'Key ['+key+'] must not present in sign');
                selfSign[key] = SELF[key];
            });
            //Строим подпись
            selfSign = PsObjects.toString(selfSign);
            selfSign = selfSign.hashCode();
            
            var getCache = function() {
                var CACHED = STORE.get(ex);
                if (//#Check cache
                    !PsIs.object(CACHED) || 
                    (CACHED.sign != selfSign)
                    //#Check cache
                    ) {
                    CACHED = {
                        sign: selfSign
                    }
                    STORE.put(ex, CACHED);
                }
                return CACHED;
            }
            
            this.has = function(n) {
                return getCache().hasOwnProperty(n);
            }
            
            this.get = function(n) {
                return PsObjects.getValue(getCache(), n);
            }
            
            this.hasAny = function() {
                var keys = PsObjects.keys2array(getCache());
                for(var i=0; i<keys.length; i++) {
                    if(PsIs.integer(keys[i])) return true;
                }
                return false;
            }
            
            this.put = function(n, info) {
                var bounds = info.df;
                if (Math.abs(n) > PsMathFuncDefInterval.MAX_STORE_INTERVAL) {
                    return false;//Интервалы с этим номером мы уже не сохраняем
                }
                if (PsIs.array(bounds) && bounds.length > PsMathFuncDefInterval.MAX_STORE_INTERVALS) {
                    return false;//---
                }
                var CACHED = getCache();
                CACHED[n] = info;
                STORE.put(ex, CACHED);
                return true;
            }
            
            this.getCache = getCache;
        }
    },
    
    /*
     *  Метод переводит координату в номер интервала, которому она принадлежит.
     */
    x2intervalNum: function(x) {
        var num, abs = Math.abs(x), sign = PsMath.sign(x);
        if (x==0 || abs==0 || sign==0) return 1;
        //Проверим целочисленное значение x
        if (PsIs.integer(abs) && (abs%this.DELTA==0)) {
            num = Math.round(abs/this.DELTA);
        } else {
            num = Math.ceil(PsMathCore.normalize(abs/this.DELTA));
        }
        
        return num==0 ? sign : sign * num;
    },
    
    intervalBounds: function(n) {
        return PsIntervals.makeSingle(PsMath.sign(n)*(Math.abs(n)-1)*this.DELTA, PsMath.sign(n)*Math.abs(n)*this.DELTA);
    },

    calc: function(options) {
        var startTime = new Date().getTime();
        
        options = $.extend({
            ex: null,        //Выраженгие для расчёта: sin(x), 1/ln(x)
            dx: null,        //Кастомный интервал проверки. Если не передан, будет подобран автоматически
            bounds: [-1, 1], //Интервал расчёта
            useCache: true,  //Признак возможности использования кеша
            ctxt:   null,    //Контекст вызова
            onProgress: function(total, current) {
            //Функция обратного вызова при обновлении прогресса
            },
            onDone: function(defIntervals) {
            //Функция обратного вызова на выполненное действие
            },
            onError: function(err) {
            //Функция обратного вызова на ошибку
            }
        }, options);
        
        //В объекте обернём функции для безопасного вызова
        options.onProgress = PsUtil.safeCall(options.onProgress, options.ctxt);
        options.onDone     = PsUtil.once(options.onDone, options.ctxt);
        options.onError    = PsUtil.once(options.onError, options.ctxt);
        
        //Проинициализируем глобальные переменные
        var LOGGER = this.logger;
        
        var JOB = PsMathEval.gatherCalcInfo(options.ex, options.bounds, options.dx);
        if (JOB.error) {
            LOGGER.logError(JOB.error);
            options.onError(JOB.error);
            return;//---
        }
        
        var ex = JOB.ex;
        var dx = JOB.dx;
        var bounds = JOB.bounds;
        
        if (JOB.error) {
            LOGGER.logError(JOB.error);
            options.onError(JOB.error);
            return;//---
        }

        //Проверим возможность расчёта одного интервала
        var JOB_INTERVAL = PsMathEval.gatherCalcInfo(ex, this.intervalBounds(1), dx);
        if (JOB_INTERVAL.error) {
            LOGGER.logError(JOB_INTERVAL.error);
            options.onError(JOB_INTERVAL.error);
            return;//---
        }
        //Зафиксируем примерное кол-во операций расчёта на интервал (для прогресса)
        var CALCS_INTERVAL = JOB_INTERVAL.estimateCalcs;

        //Расчёт состоится, поэтому увеличим счётчик
        var CALLN = ++this.calls;
        var DELTA  = this.DELTA;
        var STORE  = this.getStoreAdapter(ex, dx);

        //Определим расчитываемые границы
        var boundsL = bounds[0];
        var boundsR = bounds[1];
        
        //Какие номера интервалов потребуется привлечь?
        var intLnum = this.x2intervalNum(boundsL);
        var intRnum = this.x2intervalNum(boundsR);
        var intCnt  = 0, _i_;
        for (_i_=intLnum; _i_<=intRnum; _i_++) {
            if (_i_!=0) ++intCnt;//---
        }
        assert(intCnt>0, 'intCnt > 0');
        
        var CALCS_TOTAL = intCnt * CALCS_INTERVAL;
            
        options.onProgress(CALCS_TOTAL, 0);

        LOGGER.logWarn('{}. Запрошена ООФ [{}] на интервале {}. Задействуются интервалы с номерами {} (всего: {}). Длина интервала: {}. Шаг: {} ({}). Оценочное кол-во операций расчёта: {} ({} на интервал).', 
            CALLN, ex, bounds, [intLnum, intRnum], intCnt, DELTA, dx, JOB.dxUser ? 'user defined' : 'auto', CALCS_TOTAL, CALCS_INTERVAL);
        
        if (STORE.hasAny()) {
            LOGGER.logDebug('{}. Информация в кеше: {}', CALLN, STORE.getCache());
        }

        var WAITER = {
            waits: 0,
            results: {},
            progress: {},
            currentProgress: 0,
            onProgress: function(n, total, current) {
                this.progress[n] = Math.min(current, CALCS_INTERVAL);
                var made = 0;
                for(var v in this.progress) {
                    made+=this.progress[v];
                }
                if (made>this.currentProgress) {
                    this.currentProgress = made;
                    options.onProgress(CALCS_TOTAL, made);
                }
            },
            onSuccess: function(n, nbounds, fromCache, info) {
                if(!this.progress.hasOwnProperty(n) || this.progress[n]<CALCS_INTERVAL) {
                    this.onProgress(n, CALCS_INTERVAL, CALCS_INTERVAL);
                }
                LOGGER.logTrace('{}. ООФ на интервале №{} {} {}: {}.', CALLN, n, nbounds, fromCache ? 'найдена в кеше' : 'расчитана', info);
                this.results[n] = {
                    success: true,
                    info: info
                }
                this.decreaseAndCheck();
            },
            onError: function(n, nbounds, error) {
                LOGGER.logError('{}. ООФ на интервале №{} {} расчитана с ошибкой: {}.', CALLN, n, nbounds, error);
                this.results[n] = {
                    success: false,
                    error: error
                }
                this.decreaseAndCheck();
            },
            decreaseAndCheck: function() {
                if (--this.waits!=0) return;//---
                LOGGER.logTrace('{}. Расчёт ООФ полностью завершён. Результат: {}.', CALLN, this.results);
                
                var defIntervals = [];
                var error = false;
                var evalsTotal = 0;
                PsObjects.keys2array(this.results).walk(function(inum) {
                    var obj = this.results[inum];
                    if (obj.success) {
                        evalsTotal+= obj.info.evals;
                        defIntervals.push(obj.info.df);
                    } else {
                        error = true;
                        options.onError(obj.error);
                    }
                }, false, this);
                defIntervals = PsIntervals.intersect2(defIntervals, bounds);
                
                if (!error) {
                    options.onDone({
                        dx: dx,
                        evals: evalsTotal,
                        timeTotal: new Date().getTime() - startTime
                    }, {
                        df: defIntervals
                    });
                }
            }
        }
        
        ++WAITER.waits;
        for (_i_=intLnum; _i_<=intRnum; _i_++) {
            if (_i_==0) continue;//---
            var nbounds = this.intervalBounds(_i_);
            LOGGER.logTrace('{}. Запускаем расчёт ООФ на интервале №{} {}.', CALLN, _i_, nbounds);
            ++WAITER.waits;
            this.calcImpl(CALLN, STORE, ex, dx, _i_, nbounds, options.useCache, PsUtil.safeCall(WAITER.onProgress, WAITER), PsUtil.once(WAITER.onSuccess, WAITER), PsUtil.once(WAITER.onError, WAITER));
        }
        WAITER.decreaseAndCheck();
    },
    
    calcImpl: function(CALLN, STORE, ex, dx, n, nbounds, useCache, onProgress, onSuccess, onError) {
        var LOGGER = this.logger;

        /*
        var StoreObj = {
            delta: 20,
            dx: dx,
            n1: df
        }
        */
        //useCache=false не означает, что мы не будем искать в кеше, но сохранять в него всё равно будем
        if (useCache && STORE.has(n)) {
            var info = STORE.get(n);
            LOGGER.logTrace('{}. Данные по интервалу №{} {} найдены в хранилище: {}', CALLN, n, nbounds, info);
            onSuccess(n, nbounds, true, info);
            return;//---
        }
        
        if (PsWorkersClient.isEnabled) {
            LOGGER.logTrace('{}. ООФ на интервале №{} {} будет рассчитана при помощи воркеров с шагом {}.', CALLN, n, nbounds, dx);
            PsWorkersClient.execute({
                worker: 'func_domain',
                ctxt: this,
                data: {
                    ex: ex,
                    dx: dx,
                    bounds: nbounds
                },
                onProgress: function(total, current) {
                    onProgress(n, total, current);
                },
                onOk: function(info) {
                    if (STORE.put(n, info)) {
                        LOGGER.logTrace('{}. Результаты расчёта ООФ на интервале №{} {} сохранены в кеш.', CALLN, n, nbounds);
                    }
                    onSuccess(n, nbounds, false, info);
                },
                onErr: function(error) {
                    onError(n, nbounds, error);
                }
            });
        }
    }
}
