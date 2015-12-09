var doAction = function(msg, CONTROLLER, LOGGER) {
    CONTROLLER.error('Функция doAction не была переопределена!');
}

//Метод выполняет импорт необходимых нам ресурсов, защищаясь от повторного вызова doImport и onmessage
var doImportDone = false;
var doImport = function(NOCACHE, resources) {
    if (doImportDone) {
        throw new Error('Cannot call doImport twice.');
    } else {
        doImportDone = true;
    }
    //Подключаем необходимые нам ресурсы
    for(var i=0; i<resources.length; i++) {
        importScripts('/resources/scripts/'+resources[i]+'?_='+NOCACHE);
    }
}

self.onmessage = function(e) {
    var MSG = e.data;
    
    var DATA = MSG.data;
    var NOCACHE = MSG.nocache;
    var WORKER_NAME = MSG.worker;
    var TASK_NUM_GLOBAL = MSG.taskNumGlobal;
    var TASK_NUM_WORKER = MSG.taskNumWorker;
    
    doImport(NOCACHE, ['ps/core.js', 'ps/core.math.js', 'ps/common.workers.base.js', 'workers/'+WORKER_NAME+'.js']);
    
    //Логирование в консоль - всегда отключено
    PsLogger.logConsole = false;
    //Просто пересылаем все логи на клиента
    PsLogger.addOnLogChangedListener(function(logEvents) {
        logEvents.walk(function(logEvent) {
            postMessage(PsWorkersHelper.srvMsgLog(logEvent));
        });
    });
    
    //Создаём контроллер для обратной связи
    var CONTROLLER = {
        success: function(msg) {
            postMessage(PsWorkersHelper.srvMsgOnSuccess(msg));
        },
        
        error: function(msg) {
            postMessage(PsWorkersHelper.srvMsgOnError(msg));
        }
    }
    
    var LOGGER = PsWorkersHelper.getLogger(WORKER_NAME, TASK_NUM_WORKER, false);

    LOGGER.logInfo('Данные получены, начинаем выполнение.');
    
    try {
        doAction(DATA, CONTROLLER, LOGGER);
    } catch (err) {
        CONTROLLER.error(err);
    }
}
