$(function() {
    var FMANAGER = PsFoldingManager.FOLDING('pl', 'mathfuncs');
    var $BOX = $('#pl-mathfuncs');
    var STORE = FMANAGER.store();
    
    /*
     * Кнопки. Создадим сразу, чтобы управлять активностью
     */
    var $BUTTONS = $BOX.find('.buttons>button').button();

    /*
     * Прогресс бар и панель результатов
     */
    var $PROGRESS = $('.progress');
    var $RESULTS  = $('.results');

    /*
     * Элементы ввода
     */
    var $INPUTS = $BOX.find('input');

    /*
     * Выделим все инпуты отдельно и сбросим их значения
     */
    var $INPUT_FUNC = $INPUTS.filter('[name="func"]').val('tg(x)').enable();
    var $INPUT_CHECK_X = $INPUTS.filter('[name="checkX"]').val('0').enable();
    var $INPUT_FROM_X = $INPUTS.filter('[name="fromX"]').val('-10').enable();
    var $INPUT_TO_X = $INPUTS.filter('[name="toX"]').val('10').enable();
    var $INPUT_CACHE = $INPUTS.filter('[name="useCache"]').enable();

    //Метод извлекает из поля - число
    var field2number = function($input) {
        var value =  PsMathEval.evalSave($.trim($input.val()));
        return PsIs.number(value) ? 1*value : null;
    }
    
    //Вункция валидация поля ввода
    var validateField =  function() {
        //Фнкция отметки об инвалидности
        function markInvalid($input, text) {
            $input.addClass('invalid').attr('title', text);
        }
        
        var $input = $(this).removeClass('invalid').removeAttr('title');
        var value = $.trim($input.val());
        var name = $input.attr('name');
        var isNumber = $input.is('.number');
        var isRequired = $input.isRequired();
        if ((isRequired && !value) || (!!value && isNumber && field2number($input)===null)) {
            markInvalid($input, 'Введите ' + (isNumber ? 'число': 'значение'));
        } else {
            STORE.put(name, value);
        }
        $BUTTONS.first().uiButtonSetEnabled(!$INPUTS.is('.invalid'));
    }
    
    //Сохраним дефолтные значения, установим предыдущие и повесим слушатели изменения
    var INPUT_DEFAULTS = {};
    $INPUTS.filter('[type="text"]').each(function() {
        var $input = $(this);
        var name = $input.attr('name');
        INPUT_DEFAULTS[name] = $.trim($input.val());
        if (STORE.has(name)) {
            $input.val(STORE.get(name));
        }
    }).keyup(validateField).change(validateField).change();
    
    //Разберёмся с чекбоксами
    $INPUTS.filter('[type="checkbox"]').each(function() {
        var $input = $(this);
        var name = $input.attr('name');
        if (STORE.has(name)) {
            $input.setChecked(!!STORE.get(name));
        }
    }).change(function() {
        var $input = $(this);
        var name = $input.attr('name');
        STORE.put(name, $input.isChecked());
    });
    
    
    /*
         * Расчёт значения функции в заданной точке
         */
    var $INPUT_CHECK_X_RESULT = $INPUT_CHECK_X.siblings('span');
    var processCheckX = function() {
        $INPUT_CHECK_X_RESULT.empty();
        var func = $.trim($INPUT_FUNC.val());
        var checkX = field2number($INPUT_CHECK_X);
        if(!func || checkX===null) return;//---
        try {
            $INPUT_CHECK_X_RESULT.html(PsHtml.num2str(PsMathEval.eval(func, checkX)));
        } catch(e) {
            $INPUT_CHECK_X_RESULT.append(span_error(PsUtil.extractErrMsg(e)));
        }
        
    }
    $INPUT_FUNC.change(processCheckX).keyup(processCheckX);
    $INPUT_CHECK_X.change(processCheckX).keyup(processCheckX).change();
    
    /*
         * Кнопки - слушатели
         */
    //Кнопка ОЧИСТИТЬ
    $BUTTONS.last().click(function() {
        $INPUTS.each(function() {
            var $input = $(this);
            $input.val(PsObjects.getValue(INPUT_DEFAULTS, $input.attr('name'), ''));
        }).change();
        //Очищаем хранилище
        STORE.reset();
        //Сокус на поле ввода
        $INPUTS.first().focus();
        
        //Прячем предыдущие вычисления
        $PROGRESS.hide();
        $RESULTS.hide();
    });
    
    
    //Кнопка РАССЧИТАТЬ
    $BUTTONS.first().click(function() {
        var UM = new PsUpdateModel(null, function() {
            $INPUTS.disable();
            $BUTTONS.uiButtonDisable();
        }, function() {
            $INPUTS.enable();
            $BUTTONS.uiButtonEnable();
        });
        
        UM.start();
        
        $RESULTS.empty().hide();
        $PROGRESS.hide();
        
        var func = $.trim($INPUT_FUNC.val());
            
        PsMathFuncDefInterval.calc({
            ex: func,          //Выраженгие для расчёта: sin(x), 1/ln(x)
            bounds: [field2number($INPUT_FROM_X), field2number($INPUT_TO_X)],//Интервал расчёта
            ctxt:   null,   //Контекст вызова 
            useCache: $INPUT_CACHE.isChecked(),
            dx: 0.01,
            onProgress: function(total, current) {
                $PROGRESS.psProgressbarUpdate(total, current);
                $PROGRESS.show();
            },
            onDone: function(calcInfo, funcInfo) {
                //Функция обратного вызова на выполненное действие

                funcInfo.df = PsArrays.toArray(funcInfo.df);

                var $ol = $('<ol>');
                funcInfo.df.walk(function(df) {
                    $ol.append($('<li>').html(df.asString()));
                });
                
                $RESULTS.append('<h4>Результаты расчёта:</h4>');
                
                $RESULTS.
                append($('<div>').html('<b>'+func+'</b>')).
                append($('<br>')).
                append($('<div>').html('calcInfo='+PsObjects.toString(calcInfo))).
                append($('<br>')).
                append($('<div>').html('calcTime = '+calcInfo.timeTotal+' msec (~'+PsUtil.ms2s(calcInfo.timeTotal)+' sec)')).
                append($('<br>')).
                append($('<div>').html('Решений: <b>'+funcInfo.df.length+'</b>')).
                append($ol).
                append($('<br>')).
                append($('<div>').html('Store: '+PsObjects.toString(PsMathFuncDefInterval.store.toObject()[func]))).
                append($('<br>')).
                append($('<div>').html('Log events: '+PsLogger.events.length)).
                append($('<br>')).
                append($('<div>').html('Store keys: '+PsMathFuncDefInterval.store.keys().length));
                
                $RESULTS.show();
                
                UM.stop();
            },
            onError: function(error) {
                alert(error);
                UM.stop();
            }
        });
        
    });
});