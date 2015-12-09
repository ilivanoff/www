/**
 * Клиент для передачи задач воркерам и отслеживания прогресса выполнения.
 */
var PsWorkersClient = {
    isEnabled: PsCore.hasWorker,
    tasks: [],
    worker2tasknum: {}, //Карта Исполнитель->Номер задачи в рамках этого исполнителя
    MAX: 8,            //Максимальное количество одновременно выполняемых потоков
    CURRENT: 0,         //Текущий номер задачи (уникальный код)
    PROGRESS: 0,        //Кол-во потоков, выполняемых в данный момент
    STARTER: '/resources/scripts/workers/starter.js?_='+PsCore.startTime,
    
    /**
     * Выполнение задачи
     */
    execute: function (options) {
        
        delete options['makeClientRequest'];
        
        options = $.extend({
            taskNumGlobal: null,    //Глобальный номер задачи
            taskNumWorker: null,    //Номер задачи для этого потока
            worker: null,           //Название потока обработки
            data: {},               //Данные для исполнения
            dataStr: '{}',          //Данные в виде строки
            logger: null,           //Клиентский логгер для воркера
            ctxt: null,             //Контекст вызова функций
            onProgress: function(total, current) {
            //Функция, вызываемая при изменении состояния
            },
            onOk: function(msg) {
            //Функция, вызываемая при успешном завершении работы
            },
            onErr: function(err) {
            //Функция, вызываемая при возникновении ошибки
            },
            onDone: function(result) {
            //Функция, вызываемая по окончании работы
            },
            makeClientRequest: function() {
                //Функция преобразует данный объект в запрос к серверу
                return {
                    taskNumGlobal: this.taskNumGlobal,
                    taskNumWorker: this.taskNumWorker,
                    worker: this.worker,
                    data: this.data,
                    nocache: PsCore.startTime
                }
            }
        }, options);

        var WORKER_NAME = options.worker;
        
        if(!this.worker2tasknum.hasOwnProperty(WORKER_NAME)) {
            this.worker2tasknum[WORKER_NAME] = 0;
        }

        var TASK_NUM_GLOBAL = ++this.CURRENT;
        var TASK_NUM_WORKER = ++this.worker2tasknum[WORKER_NAME];

        options.taskNumGlobal = TASK_NUM_GLOBAL;
        options.taskNumWorker = TASK_NUM_WORKER;
        options.dataStr = PsObjects.toString(options.data);
        options.logger = PsWorkersHelper.getLogger(WORKER_NAME, TASK_NUM_WORKER, true);
        
        this.tasks.push(options);

        options.logger.logInfo('> Задача #{} запланирована. Выполняется {}/{}. Параметры: {}.', 
            TASK_NUM_GLOBAL, 
            this.PROGRESS, 
            this.MAX, 
            options.dataStr);

        this.__executeImpl();
    },

    //#1
    __executeImpl: function() {
        if (this.PROGRESS >= this.MAX) {
            return; //Достигнуто максимальное кол-во задач в стеке ---
        }
        if (this.tasks.length==0) {
            return; //Нет задач ---
        }
        
        //Сразу увеличиваем число выполняющихся задач
        ++this.PROGRESS;
        
        var OPTIONS = this.tasks.shift();
        
        var WORKER = OPTIONS.worker;
        var TASK_NUM_GLOBAL = OPTIONS.taskNumGlobal;
        var TASK_NUM_WORKER = OPTIONS.taskNumWorker;
        
        var CLIENT_REQUEST = OPTIONS.makeClientRequest();
        
        var LOGGER = OPTIONS.logger;
        var SECUNDOMER = new PsSecundomer();
        
        LOGGER.logInfo('>> Задача #{} поступила в обработку. Выполняется {}/{}. Параметры: {}', 
            TASK_NUM_GLOBAL, 
            this.PROGRESS, 
            this.MAX, 
            OPTIONS.dataStr);
        
        
        //Подготавливаем Worker
        var IMPL = new Worker(this.STARTER);

        var decodeResponse = function(workerResponse) {
            if (IMPL == null) return;//---
            
            var msg = workerResponse.msg;
            
            switch (workerResponse.type) { //#switch
                case PsWorkersHelper.TYPE.LOG:
                    
                    //Просто отлогируем событие из контекста воркеров в клиенском контексте
                    PsLogger.logEvent(msg);
                    
                    if (msg.isProgress && PsIs.func(OPTIONS.onProgress)) {
                        OPTIONS.onProgress.call(OPTIONS.ctxt, msg.total, msg.current);
                    }
                    
                    break;//---
                
                case PsWorkersHelper.TYPE.SUCCESS:
                case PsWorkersHelper.TYPE.ERROR:
                    
                    IMPL.terminate();
                    IMPL = null;
                    
                    var callResult = null;
                    
                    switch (workerResponse.type) {
                        case PsWorkersHelper.TYPE.SUCCESS:
                            
                            //Не будем писать msg в лог - пусть воркер решает, что можно показать, а что - нет.
                            LOGGER.logInfo('< Задача #{} выполнена за {} секунд.', 
                                TASK_NUM_GLOBAL, 
                                SECUNDOMER.stop());
                
                            if (PsIs.func(OPTIONS.onOk)) {
                                callResult = OPTIONS.onOk.call(OPTIONS.ctxt, msg);
                            }
                            break;
                        
                        case PsWorkersHelper.TYPE.ERROR:
                            
                            LOGGER.logError('! Задача #{} завершилась с ошибкой: {}.', 
                                TASK_NUM_GLOBAL, 
                                msg);
                
                            if (PsIs.func(OPTIONS.onErr)) {
                                callResult = OPTIONS.onErr.call(OPTIONS.ctxt, msg);
                            }
                            break;
                    }
                    
                    if (PsIs.func(OPTIONS.onDone)) {
                        OPTIONS.onDone.call(OPTIONS.ctxt, callResult);
                    }
                    
                    //Отправим следующую задачу на обработку.
                    --this.PROGRESS;
                    
                    this.__executeImpl();
                    
                    break;//---
            }//#switch
            
        }
        
        IMPL.onmessage = function(event) {
            decodeResponse.call(PsWorkersClient, event.data);
        }
        
        IMPL.onerror = function(event) {
            decodeResponse.call(PsWorkersClient, event);
        }

        //Стартуем секундомер
        SECUNDOMER.start();
        
        //Отправляем задачу в поток обработки
        PsUtil.scheduleDeferred(function() {
            IMPL.postMessage(CLIENT_REQUEST);
        });
    }
//#1

}