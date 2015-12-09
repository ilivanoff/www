doAction = function(data, CONTROLLER, LOGGER) {
    var ex = data.ex;
    var dx = data.dx;
    var bounds = data.bounds;

    //throw new Error('Bad :(');

    //    var current = 0;
    //    var total = Math.round((maxX-minX)/dx);
    
    LOGGER.logDebug('Начинаю вычислять ООФ [{}] на интервале {} с шагом {}', ex, bounds, dx);
    
    var result = PsMathEval.calcFuncDefInterval(ex, bounds, dx, function(total, current) {
        LOGGER.progressDebug(total, current, 'Выполнено {} из {} операций.', current, total);
    });

    function sleep(ms) {
        ms += new Date().getTime();
        while (new Date() < ms){}
    } 
    
    /*
    LOGGER.logDebug('Отладочная {} информация {}.', 1, 2);
    LOGGER.logTrace('Трассировочная {} информация {}.', 3, 4);
    for(var i=0; i<=100; i+=10) {
        LOGGER.progressDebug(100, i, 'Выполнено {} из {}!', i, 100);
        sleep(100);
    }
    */
    LOGGER.logInfo('Закончил вычисление ООФ [{}] на интервале {} с шагом {}. Результат: {}.', ex, bounds, dx, result);
    CONTROLLER.success(result);
}