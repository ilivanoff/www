//Данный файл может быть подключён как в контексте воркеров, так и в контексте страницы
var PsCore = {
    //Время начала построения страницы
    startTime: new Date().getTime(),
    
    //Проверяет, есть ли объект window
    hasWindow: typeof(window) != 'undefined',
    
    //Проверяет, подключен ли jQuery
    hasJquery: typeof(jQuery) != 'undefined',
    
    hasLocalStorage: typeof(localStorage) != 'undefined',
    
    hasConsole: typeof(console) != 'undefined' && typeof(console.log) != 'undefined',
    
    hasWorker: typeof(Worker) != 'undefined',
    
    //Английский алфавит
    EN_ALPHABET: 'abcdefghijklmnopqrstuxyvwz',
    
    //Английский алфавит + цифры
    EN_ALPHABET_NUM: 'abcdefghijklmnopqrstuxyvwz0123456789',
    
    //Точность расчётов с плавающей точкой
    CALC_ACCURACY: 13,
    
    //Минимальное число
    MIN_NUMBER: 0.0000000000001,
    
    //Возвращает тип переменной
    type: function(ob) {
        if (ob===null) return 'null';
        if (ob===undefined) return 'undefined';
        var type = {}.toString.apply(ob).split(' ')[1].toLowerCase();
        return type.substr(0, type.length-1);
    },
    
    //Проверяет тип переменной
    isType: function(ob, type) {
        return PsCore.type(ob) == type;
    }
}

/*
 * Проверка принадлежности переменной определённому типу
 */
var PsIs = {
    array: function(ob) {
        return PsCore.isType(ob, 'array');
    },
    
    func: function(ob) {
        return PsCore.isType(ob, 'function');
    },
    
    defined: function(ob) {
        return !PsCore.isType(ob, 'undefined');
    },
    
    string: function(ob) {
        return PsCore.isType(ob, 'string');
    },
    
    arguments: function(ob) {
        return PsCore.isType(ob, 'arguments');
    },
    
    object: function(ob) {
        return PsCore.isType(ob, 'object') || PsCore.isType(ob, 'array');
    },
    
    error: function(ob) {
        return (ob instanceof Error) || PsCore.isType(ob, 'error');
    },
    
    number: function(ob) {
        return (PsCore.isType(ob, 'number') || PsCore.isType(ob, 'string')) && !isNaN(ob) && ob!=='' && ob!==Number.NEGATIVE_INFINITY && ob!==Number.POSITIVE_INFINITY;
    },
    
    integer: function(ob) {
        return PsIs.number(ob) && parseInt(Number(ob), 10) == ob;
    },
    
    //Проверяет, является ли число экспоненциальным
    exponent: function(num) {
        return PsIs.number(num) && /[eE]/.test(''+num);
    },
    
    decimal: function(ob) {
        return PsIs.number(ob) && parseInt(Number(ob), 10) != ob;
    },
    
    jQuery: function(ob) {
        return PsCore.hasJquery && PsIs.object(ob) && (ob instanceof jQuery);
    },
    
    empty: function(ob) {
        return !ob || (PsIs.array(ob) && ob.length==0) || (PsIs.string(ob) && PsUtil.trim(ob).length==0) || (PsIs.jQuery(ob) && ob.size()==0);
    }
}

/*
 * Базовые утилиты
 */
var PsUtil = {
    //Удаление ведущих и замыкающих пробелов (http://code.jquery.com/jquery-2.1.1.js)
    trim: function(text) {
        if (text==null) return '';
        if (PsCore.hasJquery) return jQuery.trim(text);
        return (text+'').replace(/^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g, '');
    },
    
    //Преобразовывает параметры вызова функции в массив
    functionArgs2array: function(_arguments) {
        return Array.prototype.slice.call(_arguments);
    },
    
    //Случайный int в заданном интервале (TODO - заменить на PsRand.integer)
    nextInt: function(min, max) {
        return min + Math.floor(Math.random()*(PsUtil.toInteger(max, 10) - PsUtil.toInteger(min, 0) + 1));
    },
    
    //Единоразовый запуск таймера
    startTimerOnce: function(callback, delay, ctxt) {
        if(!PsIs.func(callback)) return;//---
        delay = PsUtil.toNumber(delay, 0);
        if (delay<=0) {
            callback.call(ctxt);
            return;//---
        }
        var waited = new Date().getTime();
        setTimeout(function() {
            waited = Math.max(new Date().getTime() - waited, 0);
            if (waited>=delay) {
                //Если мы прождали столько, сколько нужно - сразу вызовем callback для ускорения работы
                callback.call(ctxt);
            } else {
                //Из переданной задержки отнимем то, что мы уже прождали
                PsUtil.startTimerOnce(callback, delay - waited, ctxt);
            }
        }, delay);
    },
    
    //Проверяет, существует ли глобальный объект
    hasGlobalObject: function(objName) {
        return PsCore.hasWindow && PsIs.object(window[objName]);
    },
    
    //Вызывает объект из глобального контекста
    callGlobalObject: function(objName, callback) {
        if (PsUtil.hasGlobalObject(objName) && PsIs.func(callback)) {
            callback.call(window[objName]);
        }
    },
    
    //Показывает диалог сохранения файла
    showFileSaveDialog: function (url) {
        if (PsCore.hasWindow) window.open(url);
    },
    
    //Функция переводит миллисекунды в секунды. Если skipTranslate=true, перевода не будет
    ms2s: function(millicesonds, skipTranslate) {
        return skipTranslate ? millicesonds : Math.round(millicesonds/1000);
    },
    
    //Функция переводит переданный параметр в число. Если num-не число, то в число будет переведён defaultNum. Иначе - null.
    toNumber: function(num, defaultNum) {
        if (PsIs.number(num)) return 1*num;
        if (PsIs.number(defaultNum)) return 1*defaultNum;
        return null;
    },
    
    //Функция переводит переданный параметр в целое число. Если num-не целое число, то в число будет переведён defaultNum. Иначе - null.
    toInteger: function(num, defaultNum) {
        if (PsIs.integer(num)) return 1*num;
        if (PsIs.integer(defaultNum)) return 1*defaultNum;
        return null;
    },
    
    //Округление числа до n цифр после запятой
    round: function(x, n) {
        return PsIs.integer(n) && n>0 ? Math.round(x*Math.pow(10,n))/Math.pow(10,n) : Math.round(x);
    },
    
    //Возвращает время, прошедшее от начала выполнения (в миллисекундах, если не передан inSeconds=true)
    getUpTime: function(inSeconds) {
        return PsUtil.ms2s(Math.max(0, new Date().getTime() - PsCore.startTime), !inSeconds);
    },
    
    //Возвращает метку времени начала выполнения (в миллисекундах, если не передан inSeconds=true)
    getStartTime: function(inSeconds) {
        return PsUtil.ms2s(PsCore.startTime, !inSeconds);
    },
    
    //Выполняет функцию, когда наступит требуемый uptime (в миллисекундах)
    executeOnUptime: function(uptime, callback, ctxt) {
        if(!PsIs.func(callback)) return;//---
        var delay = uptime - PsUtil.getUpTime();
        if (delay <= 0) {
            callback.call(ctxt);
        } else {
            PsUtil.startTimerOnce(callback, delay, ctxt);
        }
    },
    
    //Принимает на вход функцию и возвращает функцию-обёртку, предотвращающую от повторного вызова
    once: function(callback, ctxt) {
        var done = false;
        var result = undefined;
        return function() {
            if(!done && PsIs.func(callback)) {
                done = true;
                result = callback.apply(PsIs.defined(ctxt) ? ctxt : this, arguments);
            }
            return result;//---
        }
    },
    
    //Принимает на вход функцию и оборачивает её для безопасного вызова в контексте.
    safeCall: function(callback, ctxt) {
        var isFunc = PsIs.func(callback);
        return function() {
            return isFunc ? callback.apply(ctxt, arguments) : undefined;
        }
    },
    
    //Метод будет выполнен после завершения основного потока обработки
    scheduleDeferred: function(callback, ctxt, delay) {
        if(!PsIs.func(callback)) return;//---
        //Задержка
        delay = PsUtil.toNumber(delay);
        delay = delay > 0 ? delay : 5;
        //Ф-ция обратного вызова
        var doCall = function() {
            PsUtil.startTimerOnce(callback, delay, ctxt);
        };
        //Если есть jQuery, отложим совсем в сторонку
        if (PsCore.hasJquery) {
            $(doCall);
        } else {
            doCall();
        }
    },
    
    //Метод извлекает ошибку из объекта
    extractErrMsg: function(error) {
        return PsIs.error(error) && PsIs.string(error['message']) ? error.message : error;
    }
}


/*
 * Класс для генерации случайных последовательностей
 */
var PsRand = {
    //Случайное булево значение
    bool: function() {
        return Math.random() >= 0.5;
    },
    
    /**
     * Метод генерирует случайное целое число
     * 
     * @param {Number} [min] - минимальное значение. Дефолт: 0.
     * @param {Number} [max] - максимальное значение. Дефолт: 9.
     */
    integer: function(min, max) {
        min = PsUtil.toInteger(min, 0);
        max = PsUtil.toInteger(max, 9);
        return min + Math.floor(Math.random()*(max - min + 1));
    },
    
    /**
     * Метод генерирует случайное число с плавающей точкой
     * 
     * @param {Number} [min] - минимальное значение. Дефолт: 0.
     * @param {Number} [max] - максимальное значение. Дефолт: 9.
     * @param {Number} [accuracy] - кол-во знаков после запятой.
     *                 Если не число, то кол-во знаков ничем не ограничивается.
     *                 Если accuracy<=0 вернётся целое число
     */
    decimal: function(min, max, accuracy) {
        if (PsIs.number(accuracy) && accuracy<=0) return PsRand.integer(min, max);
        min = PsUtil.toInteger(min, 0);
        max = PsUtil.toInteger(max, 9);
        var rand = min + Math.random() * (max - min);
        return PsIs.integer(accuracy) ? PsUtil.round(rand, accuracy) : rand;
    },
    
    //Метод возвращает случайный символ
    ch: function(upper, nums) {
        var ch;
        if (nums) {
            ch = PsCore.EN_ALPHABET_NUM[PsRand.integer(0, PsCore.EN_ALPHABET_NUM.length - 1)];
        } else {
            ch = PsCore.EN_ALPHABET[PsRand.integer(0, PsCore.EN_ALPHABET.length - 1)];
        }
        if (upper === true) return ch.toUpperCase();
        if (upper === false) return ch;
        return PsRand.bool() ? ch.toUpperCase() : ch;
    },
    
    /**
     * Метод генерирует случайную последовательность символов
     * length - длина генерируемой последовательности, по умолчанию 10
     * upper - использовать ли символы верхнего регистра:
     *      true - только верхний
     *      false - только нижний
     *      null - и верхний и нижний
     * nums - использовать ли цифры 0-9
     */
    string: function(length, upper, nums) {
        length = PsUtil.toInteger(length, 10);
        if (length<=0) return '';
        //Всегда будем начинать последовательность с буквы
        var res = PsRand.ch(upper, false);
        for (var i = 1; i < length; i++) {
            res += PsRand.ch(upper, nums);
        }
        return res;
    },
    
    //Возвращает псевдо-id для элементов DOM
    pseudoId: function(prefix) {
        return (prefix ? prefix : 'x') + "-" + PsRand.string(10, false, false);
    }
}

/*
 **************
 *   ARRAYS   *
 **************
 */

//Клонирование массива
Array.prototype.clone = function() {
    return this.slice(0);
}

/*
 * Метод, позволяющий 'пройтись' по массиву.
 * callback(item, idx, prev, next) - если функция вернёт что-то, то на это значение будет заменён элемент массива
 * stepInsideArays - признак вхождения во внутренние массивы, если таковые встретятся.
 */
Array.prototype.walk = function(callback, stepInsideArays, ctxt) {
    for (var i=0; i < this.length; i++) {
        if (stepInsideArays && PsIs.array(this[i])) {
            this[i].walk(callback, stepInsideArays, ctxt);
        }
        else
        {
            var res = callback.call(ctxt, this[i], i, i==0 ? null : this[i-1], i+1==this.length ? null : this[i+1]);
            //Если функция обратного вызова ничего не вернула, то элемент массива не будет заменён
            if (PsIs.defined(res)) {
                this[i] = res;
            }
        }
    }
    return this;
}


/*
 * Метод, позволяющий 'пройтись' по массиву с конца в начало.
 * callback(item, idx, prev, next) - если функция вернёт что-то, то на это значение будет заменён элемент массива
 * stepInsideArays - признак вхождения во внутренние массивы, если таковые встретятся.
 */
Array.prototype.walkBack = function(callback, stepInsideArays, ctxt) {
    for (var i=this.length-1; i >= 0; i--) {
        if (stepInsideArays && PsIs.array(this[i])) {
            this[i].walkBack(callback, stepInsideArays, ctxt);
        }
        else
        {
            var res = callback.call(ctxt, this[i], i, i==0 ? null : this[i-1], i+1==this.length ? null : this[i+1]);
            //Если функция обратного вызова ничего не вернула, то элемент массива не будет заменён
            if (PsIs.defined(res)) {
                this[i] = res;
            }
        }
    }
    return this;
}

//Перевод массива в красивую строку
Array.prototype.asString = function() {
    var res = '';
    this.walk(function(item) {
        res += res ? ', ' : '';
        res += PsIs.array(item) ? item.asString() : (PsIs.object(item) ? PsObjects.toString(item) : item) ;
    }, false);
    return '['+res+']';
}

//Перемешивает случайным образом элементы массива
Array.prototype.shuffle = function() {
    var len = this.length;
    var i = len;
    while (i--) {
        var p = PsRand.integer(0, i);
        var t = this[i];
        this[i] = this[p];
        this[p] = t;
    }
    return this;
}

//Удаление элемента из массива
Array.prototype.removeValue = function(val, comparator) {
    comparator = comparator ? comparator : function(val, item) {
        return val==item;
    }
    
    var i = this.length;
    while (--i>=0) {
        if (comparator(val, this[i])) {
            this.splice(i, 1);
        }
    }
    return this;
}

//Сравнение массивов
Array.prototype.equals = function(arr, comparator) {
    if (!PsIs.array(arr) || (this.length != arr.length)) return false;
    
    comparator = comparator ? comparator : function(a, b) {
        return a==b;
    }
    
    for (var i = 0; i < arr.length; i++) {
        if (PsIs.array(this[i])){
            if (this[i].equals(arr[i], comparator)){
                continue;
            }
            return false;
        }
        if (!comparator(this[i], arr[i])) return false;
    }
    
    return true;
}

//Поиск индекса элемента в массиве
Array.prototype.indexOf = function(val, comparator) {
    comparator = comparator ? comparator : function(val, item) {
        return val == item;
    }
    for (var i = 0; i < this.length; i++) {
        if (comparator (val, this[i])) return i;
    }
    return -1;
}

//Проверяет наличие элемента в массиве
Array.prototype.contains = function(val, comparator) {
    return this.indexOf(val, comparator)!=-1;
}

//Проверяет наличие всех значений в массиве
Array.prototype.hasAll = function(val, comparator) {
    val = PsIs.array(val) ? val : [val];
    for (var i = 0; i < val.length; i++) {
        if(!this.contains(val[i], comparator)) return false;
    }
    return true;
}

//Проверяет наличие хотябы обного значения в массиве
Array.prototype.hasOneOf = function(val, comparator) {
    val = PsIs.array(val) ? val : [val];
    for (var i = 0; i < val.length; i++) {
        if(this.contains(val[i], comparator)) return true;
    }
    return false;
}

//Удаляет N первых символов
Array.prototype.shiftN = function(N) {
    if(!PsIs.number(N)) return this;
    var shifted = 0;
    while (++shifted<=N && this.length>0) {
        this.shift();
    }
    return this;
}



/*
 * Утилитные методы для работы с массивами
 */
var PsArrays = {
    //Клонирует массив
    clone: function(arr) {
        return (PsIs.array(arr) ? arr : []).clone();
    },
    
    //Сравнивает как массивы
    equals: function(arr1, arr2, comparator) {
        return (arr1==null && arr2==null) || (PsIs.array(arr1) && arr1.equals(arr2, comparator));
    },
    
    //1-> [1], null->[], [1, 2]->[1, 2]
    toArray: function(arr) {
        if (PsIs.array(arr)) return arr;
        if (PsIs.arguments(arr)) return PsUtil.functionArgs2array(arr);
        if(!PsIs.defined(arr) || arr===null) return [];
        return [arr];
    },
    
    //Метод создаёт фильтр для массивов
    makeFilter: function(filter, ctxt) {
        var doFilter = PsIs.func(filter);
        if (doFilter && filter.hasOwnProperty('ps_arr_filt') && filter['ps_arr_filt']===true) return filter;//Повторно не оборачиваем фильтр---
        var filterImpl = function(item) {
            if (!doFilter) return {
                item: item,
                take: true
            }
            var result = filter.call(ctxt, item);
            if (PsIs.object(result) && result.hasOwnProperty('item') && result.hasOwnProperty('take')) {
                return result;
            }
            return  {
                item: item,
                take: !!result
            }
        }
        filterImpl['ps_arr_filt'] = true;
        return filterImpl;
    },
    
    /**
     * Метод для фильтрации элементов массива.
     * В качестве фильтра может быть передана как функция, так и фильтр, созданный с 
     * помощью PsArrays.makeFilter.
     * 
     * Фильтрация производится по всему массиву рекурсивно, то есть с заходом в подмассивы и потом 
     * фильтр применяется к самому отфильтрованному массиву.
     * 
     * @param {Array | mixed} [arr] Массив, элементы которого нужно отфильтровать
     * @param {Function} [filter] Функция для фильтрации элементов массива. См. PsArrays.makeFilter.
     * @return {Array} Массив с отфильтрованными элеметнами
     */
    filter: function(arr, filter) {
        arr = PsArrays.toArray(arr);
        if(!PsIs.func(filter)) return arr;//---
        filter = PsArrays.makeFilter(filter);
        var result = [];
        arr.walk(function(item) {
            if (PsIs.array(item)) {
                item = PsArrays.filter(item, filter);
            }
            var filtered = filter(item);
            if (filtered.take) {
                result.push(filtered.item);
            }
        });
        return result;
    },
    
    //Метод фильтрации пустых элементов массива (null, false, NaN, undefined). 0 - включается.
    filterEmpty: function(arr) {
        return PsArrays.filter(arr, function(item) {
            return item===0 || !PsIs.empty(item);
        });
    },
    
    //Делает массив - плоским, разворачивая все внутренние массивы
    //[1,[2,3],[4,5,[6]]] -> [1,2,3,4,5,6]
    expand: function(arr, filter) {
        filter = PsArrays.makeFilter(filter);
        var result = [];
        PsArrays.toArray(arr).walk(function(item) {
            var filtered = filter(item);
            if (filtered.take) {
                result.push(filtered.item);
            }
        }, true);
        return result;
    },
    
    /**
     * Извлекает все подмассивы, а из них - свои подмассивы. Пустые массивы не включаются.
     * Пример: [1,2,[3,4,[5,6,7,[8]]],9] -> [[1,2,9][3,4][5,6,7][8]]
     * 
     * @param {Array | mixed} [arr] Массив, в котором будут выделены подмассивы
     * @param {Function} [filter] Функция для фильтрации элементов массива. См. PsArrays.makeFilter.
     * @return {Array} Массив с развёрнутыми подмассивами
     */
    extractSubArrays: function(arr, filter) {
        filter = PsArrays.makeFilter(filter);
        
        var result = [];
        var doExpand = function(array) {
            var subArray = null;
            array.walk(function(item) {
                if (PsIs.array(item)) {
                    doExpand(item);
                } else {
                    var filtered = filter(item);
                    if (filtered.take) {
                        if (!subArray) result.push(subArray=[]);
                        subArray.push(filtered.item);
                    }
                }
            });
        }
        doExpand(PsArrays.toArray(arr));
        return result;
    },
    
    //Возвращает уникальные значения из массива. Пустые значения будут выброшены.
    unique: function(arr, comparator) {
        var result = [];
        PsArrays.toArray(arr).walk(function(item) {
            if((item===0 || !PsIs.empty(item)) && !result.contains(item, comparator)) {
                result.push(item);
            }
        });
        return result;
    },
    
    //Вычисляет разницу двух массивов
    getDiff: function(arr1, arr2, comparator) {
        arr1 = PsArrays.unique(arr1, comparator).sort();
        arr2 = PsArrays.unique(arr2, comparator).sort();
        
        var comm = [];
        var diff = [];
        var a1HasOnly = [];
        var a2HasOnly = [];
        
        PsArrays.unique(arr1.concat(arr2), comparator).walk(function(item) {
            var has1 = arr1.contains(item, comparator);
            var has2 = arr2.contains(item, comparator);
            
            if (has1 && has2) {
                comm.push(item);
            } else {
                diff.push(item);
                if(has1) a1HasOnly.push(item);
                if(has2) a2HasOnly.push(item);
            }
        });
        
        return {
            comm: comm.sort(),
            diff: diff.sort(),
            a1has: a1HasOnly.sort(),
            a2has: a2HasOnly.sort()
        }
    },
    
    //Разбивает массив на массивы по n элементов split2arrays([1,2,3,4], 2) -> [[1,2],[3,4]]
    array2arrays: function(arr, n) {
        var res = [];
        PsArrays.toArray(arr).walk(function(item) {
            if(!res.length || (res[res.length-1].length==n)) {
                res.push([item]);
            } else {
                res[res.length-1].push(item);
            }
        }, true);
        return res;
    },
    
    //Разбивает строку на массив string2arrays('1234', 2) -> [[1,2],[3,4]]
    string2arrays: function(str, n) {
        return this.array2arrays(str.split(''), n);
    },
    
    //Метод склеивает все элементы в строку, предварительно разворачивая массив
    joinExpanded: function(arr, glue) {
        return PsArrays.expand(arr).join(PsIs.defined(glue) ? ''+glue : '');
    },
    
    //Проверяет, присутствует ли элемент в массиве
    inArray: function(value, arr, comparator) {
        return PsArrays.toArray(arr).contains(value, comparator);
    },
    
    //Первый элемент массива
    firstItem: function(arr) {
        return PsIs.array(arr) && arr.length>0 ? arr[0] : undefined;
    },
    
    //Последний элемент массива
    lastItem: function(arr) {
        return PsIs.array(arr) && arr.length>0 ? arr[arr.length-1] : undefined;
    },
    
    //Возвращает следующий элемент массива или null
    nextItem: function(arr, item, comparator) {
        var idx = arr.indexOf(item, comparator);
        return idx<0 || idx==arr.length-1 ? null : arr[idx+1];
    },
    
    //Возвращает следующий элемент массива или, если его нет - первый
    nextOrFirstItem: function(arr, item, comparator) {
        var idx = arr.indexOf(item, comparator);
        return idx<0 || idx==arr.length-1 ? (arr.length==0 ? null : arr[0]) : arr[idx+1];
    },
    
    //Возвращает центральный элемент массива
    centralItem: function(arr) {
        return arr.length ? arr[Math.floor((arr.length-1)/2)] : null;
    }
}

/*
 ***************
 *   OBJECTS   *
 ***************
 */

var PsObjects = {
    //Возвращает массив ключей объекта
    keys2array: function(obj){
        if(!obj) return [];
        var res = [];
        for (var v in obj) {
            if(!obj.hasOwnProperty(v)) continue;
            res.push(v);
        }
        return res.sort();
    },
    
    //Проверяет, есть ли в объекте ключи
    hasKeys: function(obj) {
        for (var v in obj) {
            if (obj.hasOwnProperty(v)) return true;
        }
        return false;
    },
    
    //Получает значение из массива
    getValue: function(obj, key, dflt) {
        return PsIs.object(obj) && obj.hasOwnProperty(key) ? obj[key] : (PsIs.defined(dflt) ? dflt : null);
    },
    
    //Проверяет, есть ли у объекта свойство с заданным значением
    hasValue: function(obj, val, comparator) {
        comparator = comparator ? comparator : function(val, item) {
            return val==item;
        }
        for (var v in obj) {
            if (!obj.hasOwnProperty(v)) continue;//---
            if (comparator(val, obj[v])) {
                return true;//---
            }
        }
        return false;
    },
    
    //Преобразует объект в строку. Может использоваться как hash, так как ключи будут отсортированы
    toString: function(obj) {
        if (PsIs.array(obj)) return obj.asString();
        if (PsIs.arguments(obj)) return PsUtil.functionArgs2array(obj).asString();
        if (!PsIs.object(obj)) return ''+obj;
        
        var res = '';
        this.keys2array(obj).walk(function(key) {
            res+= res ? ', ' : '';
            res+= key + ': ' + PsObjects.toString(obj[key]);
        });
        return '{'+res+'}';
    },
    
    //Используется для toString бинов, выкидывая из объекта все функции и, если нужно, данные с ключами excludeKeys
    toStringData: function(obj, excludeKeys) {
        return this.toString(this.clone(obj, excludeKeys, true));
    },
    
    //Преобразовывает значения из объекта в массив. Если нужно - ещё отсортирует значения.
    values2array: function(obj, comparator) {
        var res = [];
        this.keys2array(obj).walk(function(key) {
            res.push(obj[key]);
        });
        if (comparator) {
            res.sort(comparator);
        }
        return res;
    },
    
    //Метод глубокого клонирования объекта с исключением переданных ключей
    clone: function(obj, excludeKeys, excludeFuncs, forceIncludeKeys) {
        if (PsIs.array(obj)) return PsArrays.clone(obj);
        if(!PsIs.object(obj)) return obj;
        
        excludeKeys = PsArrays.toArray(excludeKeys);
        forceIncludeKeys = PsArrays.toArray(forceIncludeKeys);
        
        var cloned = {};
        
        for (var k in obj) {
            if (!forceIncludeKeys.contains(k)) {
                if (excludeKeys.contains(k)) continue;//---
                if (excludeFuncs && PsIs.func(obj[k])) continue;//---
            }
            cloned[k] = PsObjects.clone(obj[k], [], excludeFuncs, forceIncludeKeys);
        }
        
        return cloned;
    }
}

/*
 ***************
 *   STRINGS   *
 ***************
 */

//Проверка вхождения подстроки в строку
String.prototype.contains = function(s) {
    return this.indexOf(s) != -1;
}

//Проверка, начинается ли строка на переданную постедовательность
String.prototype.startsWith = function(s) {
    return this.indexOf(s) == 0;
}

//Удеждается, что строка начинается с указанной подстроки
String.prototype.ensureStartsWith = function (s) {
    return '' + (this.startsWith(s) ? this : s + this);
}

//Проверка, заканчивается ли строка на переданную постедовательность
String.prototype.endsWith = function (s) {
    return s!=null && (this.length >= s.length && (this.length-s.length)==this.lastIndexOf(s));
}

//Удеждается, что строка заканчивается указанной подстрокой
String.prototype.ensureEndsWith = function (s) {
    return '' + (this.endsWith(s) ? this : this + s);
}

//Эскейпирует символы для вставки в html
String.prototype.htmlEntities = function () {
    return this.replace(/&/g,'&amp;').replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/\n/g, '<br/>');
};

//Преобразует первый символ с верхний регистр
String.prototype.firstCharToUpper = function() {
    return this.charAt(0).toUpperCase() + this.slice(1);
}

String.prototype.hasJax = function () {
    return (this.indexOf('\\(')!=-1 && this.indexOf('\\)')!=-1) || 
    (this.indexOf('\\[')!=-1 && this.indexOf('\\]')!=-1) || 
    (this.indexOf('\\{')!=-1 && this.indexOf('\\}')!=-1) || 
    this.indexOf('$$')!=-1;
};

String.prototype.hasJaxSymbols = function () {
    return this.indexOf('\\(')!=-1 || 
    this.indexOf('\\)')!=-1 || 
    this.indexOf('\\[')!=-1 || 
    this.indexOf('\\]')!=-1 || 
    this.indexOf('\\{')!=-1 || 
    this.indexOf('\\}')!=-1 || 
    this.indexOf('$$')!=-1;
};

//перемешивает символы в строке
String.prototype.shuffle = function () {
    return this.split('').shuffle().join('');
}

//Подсчитывает кол-во символов в строке
String.prototype.charsCnt = function(ch) {
    var cnt = 0;
    this.split('').walk(function(_ch) {
        if(_ch==ch) ++cnt;
    });
    return cnt;
}

//Метод возвращает cnt первых символов
String.prototype.getFirstChar = function(cnt) {
    return '' + this.substr(0, Math.min(this.length, Math.max(0, PsUtil.toInteger(cnt, 1))));
}

//Удаляет cnt первых символов
String.prototype.removeFirstChar = function(cnt) {
    return '' + this.substr(Math.min(this.length, Math.max(0, PsUtil.toInteger(cnt, 1))), this.length);
}

//Удаляет начало строки, если она совпадает с ch
String.prototype.removeFirstCharIf = function(ch) {
    return '' + (this.startsWith(ch) ? this.removeFirstChar(ch.length) : this);
}

//Удаляет все символы ch в конце строки
String.prototype.removeFirstCharWhile = function(ch) {
    var self = '' + this;
    while (self.startsWith(ch)) {
        self = self.removeFirstChar(1);
    }
    return self;
}

//Метод возвращает cnt последних символов
String.prototype.getLastChar = function(cnt) {
    return '' + this.substr(this.length-Math.min(this.length, Math.max(0, PsUtil.toInteger(cnt, 1))));
}

//Удаляет cnt последних символов
String.prototype.removeLastChar = function(cnt) {
    return '' + this.substr(0, this.length - Math.min(this.length, Math.max(0, PsUtil.toInteger(cnt, 1))), this.length);
}

//Удаляет конец строки, если он совпадает с ch
String.prototype.removeLastCharIf = function(ch) {
    return '' + (this.endsWith(ch) ? this.removeLastChar(ch.length) : this);
}

//Удаляет все символы ch в конце строки
String.prototype.removeLastCharWhile = function(ch) {
    var self = '' + this;
    while (self.endsWith(ch)) {
        self = self.removeLastChar(1);
    }
    return self;
}

//Заменяет все вхождения подстроки search на replace
String.prototype.replaceAll = function(search, replace) {
    return this.split(search).join(replace);
}

/*
 * Хэш код строки, как в java. Подсмотрено:
 * http://stackoverflow.com/questions/7616461/generate-a-hash-from-string-in-javascript-jquery
 */
String.prototype.hashCode = function() {
    var hash = 0, i, chr, len;
    if (this.length == 0) return hash;
    for (i = 0, len = this.length; i < len; i++) {
        chr   = this.charCodeAt(i);
        hash  = ((hash << 5) - hash) + chr;
        hash |= 0; // Convert to 32bit integer
    }
    return hash;
}

/*
 * Стандартный split с передачей лимита отрезает конец строки и не возвращает его.
 * Эта функция эмулирует привычное поведение.
 * 
 * alert('a|b|c'.split('|',1)) = [a];
 * alert('a|b|c'.splitSave('|',1)) = [a|b|c];
 * 
 * alert('a|b|c'.split('|',2)) = [a, b];
 * alert('a|b|c'.splitSave('|',2)) = [a, b|c];
 */
String.prototype.splitSave = function(separator, limit) {
    var parts = this.split(separator);
    limit = PsUtil.toInteger(limit);
    if (!limit || limit<=0 || parts.length<limit) return parts;
    var result = [];
    for(var i=0; i<limit-1;i++) {
        result.push(parts.shift());
    }
    result.push(parts.join(separator));
    return result;
}

/*
 * Утилитные методы для работы со строками
 */
var PsStrings = {
    trim: PsUtil.trim,
    
    //Метод заменяет в строке where подстроку search последовательно на элементы replace
    replaceOneByOne: function(where, search, replace, itemsFilter) {
        if (!PsIs.string(where) || where.length==0) return '';
        if (!PsIs.string(search) || search.length==0) return where;
        
        PsArrays.filter(replace, itemsFilter).walk(function(replace4) {
            where = where.replace(search, '' + replace4);
        });
        return where;
    },
    
    //Метод удаляет пробелы из строки: ' a  b c ' -> 'abc'
    removeSpaces: function(text) {
        text = PsStrings.trim(text).replaceAll(' ', '');
        while (text.contains(' ')) {
            text = text.replace(' ', '')
        }
        return text;
    },
    
    //Метод добавляет в начало строки str символ sym до достижения длины length
    padLeft: function(str, sym, length) {
        str = '' + str;
        while (str.length < length) {
            str = sym + str;
        }
        return str;
    },
    
    //Метод добавляет в конец строки str символ sym до достижения длины length
    padRight: function(str, sym, length) {
        str = '' + str;
        while (str.length < length) {
            str = str + sym;
        }
        return str;
    },
    
    //Метод добавляет в конец строки str символ sym до достижения длины length
    fill: function(sym, length) {
        if(!sym || length<=0) return '';
        var str = '';
        while (str.length < length) {
            str = str + sym;
        }
        return str.substring(0, length);
    },
    
    //Метод преобразует текст в шаблон для регулярного выражения, эскейпируя специальные символы, которые могут входить в шалон: +, [, ] и т.д.
    regExpQuantifier: function (text) {
        return (PsIs.string(text) ? text : '').replace(/([*+.?|\\\[\]{}()])/g, '\\$1');
    }
}



/*
 * Логгер
 * 
 * Каждый класс имеет свой логгер и пишет под своим префиксом.
 * Каждый такой логгер может иметь свой уровень логирования или вообще быть отключён.
 * 
 * Каждое сообщение, которое пишется в лог, должно иметь свой уровень лигирования.
 * Если этот уровень не указан, берётся дефолтный.
 */
var PsLogger = {
    //Уровни логирования
    LEVELS: {
        Error: 1,
        Warn: 2,
        Info: 3,
        Debug: 4,
        Trace: 5
    },
    
    //Уровень логирования по умолчанию.
    LEVEL_DEFAUL: 3,
    
    /*
     * Признак логирования в консоль. Если поставить false, то даже при наличии
     * консоли в неё ничего писаться не будет.
     */
    logConsole: true,
    
    //Возвращает код уровня логирования
    levelCode: function(level) {
        level = PsIs.integer(level) ? 1 * level : PsObjects.getValue(this.LEVELS, level);
        return level && PsObjects.hasValue(this.LEVELS, level) ? level : PsLogger.LEVEL_DEFAUL;
    },
    
    //Возвращает название уровня логирования
    levelName: function(level) {
        var code = this.levelCode(level);
        for (var v in this.LEVELS) {
            if (this.LEVELS[v]==code) {
                return v;
            }
        }
        return null;
    },
    
    insts: {},
    inst: function(prefix) {
        return this.insts.hasOwnProperty(prefix) ? this.insts[prefix] : this.insts[prefix] = new function Logger() {
            var enabled = true;
            var logLevel = PsLogger.LEVEL_DEFAUL;//Стартуем с дефолтным уровнем логирования
            
            var inst = this;
            
            var isLevel = function(level) {
                return PsLogger.levelCode(level) <= logLevel;
            }
            
            var setLevel = function(level) {
                logLevel = PsLogger.levelCode(level);
                return inst;
            }
            
            var doLogParams = function(level, msg, params, total, current) {
                if (enabled && isLevel(level)) {
                    PsLogger.log(msg, params, prefix, level, total, current);
                }
                return inst;
            }
            
            this.enable = function() {
                enabled = true;
                return this;
            }
            this.disable = function() {
                enabled = false;
                return this;
            }
            
            //Прямые методы работы с уровнем логирования
            this.log = function(level, msg) {
                return doLogParams(level, msg, PsArrays.toArray(arguments).shiftN(2));
            }
            
            this.progress = function(level, total, current, msg) {
                return doLogParams(level, msg, PsArrays.toArray(arguments).shiftN(4), total, current);
            }
            
            this.logParams = function(level, msg, params) {
                return doLogParams(level, msg, params);
            }
            
            this.set = function(level) {
                return setLevel(level);
            }
            
            this.is = function(level) {
                return isLevel(level);
            }
            
            //Методы {is, log, set} для работы с логом (установка вынесена в отдельную функцию из-за замыкания)
            var addMethods = function(level) {
                //isInfo
                this['is'+level] = function() {
                    return isLevel(level)
                }
                //logInfo
                this['log'+level] = function(msg) {
                    return doLogParams(level, msg, PsArrays.toArray(arguments).shiftN(1));
                }
                //progressInfo
                this['progress'+level] = function(total, current, msg) {
                    return doLogParams(level, msg, PsArrays.toArray(arguments).shiftN(3), total, current);
                }
                //logInfoParams
                this['log'+level+'Params'] = function(msg, params) {
                    return doLogParams(level, msg, params);
                }
                //setInfo
                this['set'+level] = function() {
                    return setLevel(level);
                }
            }
            
            for (var level in PsLogger.LEVELS) {
                addMethods.call(this, level);
            }
        }
    
    //#End logger and return
    },
    
    events: [],
    log: function(msg, params, prefix, level, total, current) {
        
        var hasMessage = PsIs.defined(msg) && (!PsIs.string(msg) || !PsIs.empty(msg));
        var hasProgress = PsIs.number(total) && PsIs.number(current);
        
        if (!hasMessage && !hasProgress) {
            return;//---
        }
        
        //Не забудем преобразовать к строке, так как передать могут всё, что угодно
        if (hasMessage) {
            msg = PsStrings.replaceOneByOne(PsObjects.toString(msg), '{}', params, function(item) {
                return {
                    take: true,
                    item: PsObjects.toString(item)
                }
            });
        } else {
            msg = '';
        }
        
        //Разберёмся с прогрессом
        var percent, msgProgress = msg;
        if (hasProgress) {
            total   = 1 * total;
            current = 1 * current;
            percent = PsUtil.round(current*100/total, 1);
            msgProgress = msg ? msg + ' ('+percent+'%).' : 'Выполнено '+percent+'%.';
        }
        
        //Формируем событие лога
        var logEvent = {
            msg:         msg,
            prefix:      prefix,
            level:       this.levelCode(level),
            type:        this.levelName(level),
            isProgress:  hasProgress,
            //Если прогресс
            total:       hasProgress ? total   : undefined,
            current:     hasProgress ? current : undefined,
            percent:     hasProgress ? percent : undefined,
            msgProgress: msgProgress
        }
        
        this.logEvent(logEvent);
    },
    
    logEvent: function(logEvent) {
        //Оповестим всех слушателей о поступлении новых логов
        this.events.push(logEvent);
        this.listeners.walk(function(clbk) {
            clbk([logEvent]);
        });
        
        //Если есть консоль - запишем сообщение
        if (PsCore.hasConsole && this.logConsole) {
            console.log((logEvent.prefix ? logEvent.prefix+': ' : '') + logEvent.msgProgress);
        }
    },
    
    /**
     * Метод добавляет слушателя на запись новых логов
     */
    listeners: [],
    addOnLogChangedListener: function(callback) {
        if(!PsIs.func(callback)) return;//---
        if(this.listeners.contains(callback)) return;//---
        this.listeners.push(callback);
        if (this.events.length) {
            callback(this.events);
        }
    },
    
    //TODO - выкинуть
    format: function(_arguments) {
        var argsArr = PsUtil.functionArgs2array(_arguments);
        if(!argsArr.length) return '';//---
        return PsStrings.replaceOneByOne(PsObjects.toString(argsArr.shift()), '{}', argsArr, function(item) {
            return {
                take: true,
                item: PsObjects.toString(item)
            }
        });
    }
}

function consoleLog(msg, a) {
    PsLogger.log(msg, PsArrays.toArray(arguments).shiftN(1));
}

function assert(condition, msg) {
    if(!condition) throw new Error(msg);
}
