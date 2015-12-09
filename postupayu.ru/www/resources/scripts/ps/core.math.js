/**
 * Базовые утилиты математического модуля, отвечающие за округление чисел и
 * приведение чисел к точности вычислений.
 */
var PsMathCore = {
    /**
     * Точность вычислений с плавуающей точкой.
     * То есть какой знак после запятой ещё можно принимать во внимание.
     * Конечно лучше его задавать на константах, но всёже попытаемся вычислить,
     * а ещё лучше было бы не думать вообще о точности вычислений в javascript но увы:(
     */
    CALC_ACCURACY: 13,
    
    /**
     * Минимальное число
     */
    MIN_NUMBER: 1e-13,
    
    /**
     * Длина окончания числа, которая проверяется для выяснения, нужно ли число нормализовать
     */ 
    NORMALIZE_LENGTH: 5,
    
    
    //Инициализация основных констант математического модуля
    init: function() {
        //#1. Определим точность вычисления CALC_ACCURACY
        var fetchAccuracy = PsUtil.safeCall(function(value) {
            var string = '' + value;
            if (PsIs.exponent(string) || !string.contains('.')) return;//---
            this.CALC_ACCURACY = Math.min(this.CALC_ACCURACY, string.split('.')[1].length);
        }, this);

        //Числа пи и e
        fetchAccuracy(Math.PI);
        fetchAccuracy(Math.E);
        
        //Известная проблема. Обычно 0.1+0.2 = 0.30000000000000004
        var S0102 = '' + (0.1 + 0.2);
        if (S0102!='0.3') {
            fetchAccuracy(S0102);
        }
        
        //Проверим на точности различных тригонометрических функций
        fetchAccuracy(Math.sin(Math.PI/3));
        fetchAccuracy(Math.sin(-1*Math.PI/3));
        fetchAccuracy(180/Math.PI);
        
        //#2. Определим минимальное число в мимтеме
        this.MIN_NUMBER = 1 * ('1e-' + this.CALC_ACCURACY);
    },
    
    //Разбивает число с плавающей точкой на целую часть и часть после запятой
    splitDecimal: function(num) {
        if (!PsIs.number(num)) return null;
        
        //Сразу вернём инты
        var string = num==0 ? '0' : ''+num;
        
        //Разберёмся с экспонентой
        if (/[eE]/.test(string)) {
            string = (1 * num).toFixed(PsMathCore.CALC_ACCURACY+1);
            if (/[eE]/.test(string)) return null; //Очень большое
        }

        var tokens = string.split('.');
        var ceil = null, decimal = '';
        switch (tokens.length) {
            case 1:
                ceil = tokens[0];
                break;//#1
                
            case 2:
                ceil = tokens[0];
                decimal = tokens[1];
                decimal = decimal.substring(0, Math.min(PsMathCore.CALC_ACCURACY+1, tokens[1].length))
                decimal = decimal.removeLastCharWhile('0').removeLastCharIf('.');
                break;//#2
                
            default:
                return null;//Others
        }
        
        if (!PsIs.integer(ceil)) return null;//---
        
        return {
            c: ceil,   //Ceil
            d: decimal //Decimal
        }
    },
    
    //Возвращает точность числа c плавающей точкой: 0.1234 = 4, 0.12 = 2
    decimalAccuracy: function(decimal) {
        var splitted = PsMathCore.splitDecimal(decimal);
        return splitted ? Math.min(splitted.d.length, PsMathCore.CALC_ACCURACY) : 0;
    },
        
    //Метод приводит число с плавающей точкой к числу с размеростью не больше той, в которой производятся вычисления
    normalize: function(value, skip) {
        if (!PsIs.number(value)) return value;//---
        if (skip) return 1*value;//---
        var splitted = PsMathCore.splitDecimal(value);
        if(!splitted) return 1*value;//---
        if (splitted.d==='') return 1*splitted.c;
        if (splitted.d.length <= PsMathCore.CALC_ACCURACY) return 1*(splitted.c+'.'+splitted.d);
        assert(splitted.d.length === PsMathCore.CALC_ACCURACY+1);
        return PsUtil.round(splitted.c + '.' + splitted.d, PsMathCore.CALC_ACCURACY)
    /*
        var decimalAfter =  PsUtil.round('0.'+splitted.d, PsMathCore.CALC_ACCURACY).
        toFixed(PsMathCore.CALC_ACCURACY).removeLastCharWhile('0');
        if (PsMathCore.CALC_ACCURACY - decimalAfter.split('.')[1].length > PsMathCore.NORMALIZE_LENGTH) {
            return PsUtil.round(splitted.c + '.' + splitted.d, PsMathCore.CALC_ACCURACY)
        }
        return 1 * (splitted.c + '.' + splitted.d.substring(0, PsMathCore.CALC_ACCURACY).removeLastCharWhile('0'));
        */
    }
}
PsMathCore.init();

/*
 * Расширение математических функицй.
 * Везде вместо this нужно использовать PsMath, т.к. возможен вызов через eval(...)
 */
var PsMath = {
    //Округление числа до n цифр после запятой
    round: PsUtil.round,
    
    //Нормализация числа
    normalize: PsMathCore.normalize,
    
    //Метод проверяет, является ли число - натуральным
    isnat: function(x) {
        return PsIs.integer(x) && x>=1;
    },
    
    //Проверяет число на простоту
    isprime: function (x) {
        if (x==2) return true;
        if (x<2 || x%2==0 || !PsMath.isnat(x)) return false;
        for(var i=3;i<=Math.sqrt(x);i+=2) if(x%i==0) return false;
        return true;
    },
    
    //Метод вычисляет факториал заданного числа
    factorial: function(n) {
        if(!n || n<=1) return 1;
        var x=1;
        for(var i=2;i<=n;i++) x*=i;
        return x;
    },
    
    //Метод вычисляет факториал заданного числа, беря только простые числа
    pfactorial: function(n) {
        if(!n || n<2) return 1;
        var x = 2;
        for(var i=3;i<=n;i+=2) if(PsMath.isprime(i)) x*=i;
        return x;
    },
    
    //Простые числа от 2 до x
    primes: function(x) {
        var res = [];
        if (x>=2) res.push(2);
        for(var i=3; i<=x; i+=2) if(PsMath.isprime(i)) res.push(i);
        return res;
    },
    
    //Кол-во простых чисел среди натуральных чисел, меньших либо равных x
    primepi: function(x) {
        var k = x<2 ? 0 : 1;
        for(var i=3; i<=x; i+=2) if(PsMath.isprime(i)) ++k;
        return k;
    },
    
    //Возвращает массив чисел, взаимно простых с x (не имеют никаких общих делителей, кроме ±1)
    relprime: function (x) {
        var a = [1];
        for(var j=2;j<x;j++) if(PsMath.gcd([x,j])==1) a.push(j);
        return a;
    },
    
    //Функция Эйлера - кол-во чисел, меньших x и взаимно простых с x
    totient: function (x) {
        var k=1;
        for(var j=2;j<x;j++) if(PsMath.gcd([x,j])==1) k++;
        return k;
    },
    
    //Простые делители числа (раскладывает число на множители)
    factor: function (x) {
        var res = [];
        if (x<2) return res;
        var k = 0;
        for(var i=2; i<=Math.sqrt(x);i+=k) {
            if (k<2) k++;
            for(var j=0; x%i==0; j++)  {
                x/=i;
                res.push(i);
            }
        }
        if(PsMath.isprime(x)) res.push(x);
        return res;
    },
    
    //Квадрат числа
    sq: function(x) {
        return PsMath.normalize(x*x);
    },
    
    //TODO - перенести в другой класс
    integral: function(f, x1, x2) {
        var left = Math.min(x1, x2);
        var righ = Math.max(x1, x2);
        
        var dx = 0.001;
        
        var S = 0;
        var Sabs = 0;
        for (var x = left; x < righ; x+=dx) {
            var y = PsMathEval.evalSave(f, x);
            if(!y) continue;
            S += y*dx;
            Sabs += Math.abs(y)*dx;
        }
        S = PsMathCore.normalize(S);
        Sabs = PsMathCore.normalize(Sabs);
        
        return {
            from: left,
            to: righ,
            S: S,
            Sabs: Sabs
        }
    },
    
    //Перенести в другой класс
    derivative: function(f, x0) {
        var dx = 0.001;
        var x0dx = x0+dx;
        var fx0dx = PsMathEval.evalSave(f, x0dx);
        if (!PsIs.number(fx0dx)) return null;
        var fx0 = PsMathEval.evalSave(f, x0);
        if (!PsIs.number(fx0)) return null;
        var der = (fx0dx - fx0)/dx;
        
        return {
            x0: x0,
            fx0: fx0,
            x0dx: x0dx,
            fx0dx: fx0dx,
            der: PsMathCore.normalize(der)
        };
    },
    
    //Число Фибоначчи (каждое последующее число равно сумме двух предыдущих чисел, f0=0, f1=1, f2=1, f3=2)
    fibonacci: function (x) {
        return Math.round((Math.pow((1+Math.sqrt(5))/2, x) - Math.pow((1-Math.sqrt(5))/2, x)) / Math.sqrt(5));
    },
    
    //Знак числа
    sign: function(val) {
        return val==0 ? 0 : (val > 0 ? 1 : -1);
    },
    
    //Проверяет, совпадает ли знак числа. Если будет передан массив, то сравнит знак всех чисел.
    isSameSign: function(a, b) {
        var nums = PsIs.array(a) ? a : [a, b];
        if(!nums.length) return true;
        var sign = PsMath.sign(nums[0]);
        for (var i = 1; i < nums.length; i++) {
            if (sign!=PsMath.sign(nums[i])) return false;//---
        }
        return true;
    },
    
    //Максимальное из чисел. В качестве аргумента можно передать массив
    max: function (a, b) {
        return PsIs.number(a) ? Math.max(a, b) : 
        a.clone().sort(function(c, d) {
            return d-c;
        })[0];
    },
    
    //Минимальное из чисел. В качестве аргумента можно передать массив
    min: function (a, b) {
        return PsIs.number(a) ? Math.min(a, b) : 
        a.clone().sort(function(c, d) {
            return c-d;
        })[0];
    },
    
    
    //Складывает числа в массиве
    sum: function (a) {
        var sum = 0;
        PsArrays.toArray(arguments).walk(function(num) {
            if (PsIs.number(num)) {
                sum = PsMath.normalize(sum + PsMath.normalize(num));
            }
        }, true);
        return sum;
    },
    
    //Среднее значение
    average: function(numbers) {
        var nums = PsArrays.expand(arguments, function(item) {
            return PsIs.number(item);
        });
        return nums.length ? PsMath.normalize(PsMath.sum(nums)/nums.length) : NaN;
    },
    
    //Перемножает числа в массиве
    product: function (a) {
        var result = NaN;
        PsArrays.toArray(arguments).walk(function(num) {
            if (PsIs.number(num)) {
                if (isNaN(result)) {
                    result = PsMath.normalize(num);
                } else {
                    result = PsMath.normalize(result * PsMath.normalize(num));
                }
            }
        }, true);
        return result;
    },
    
    //Возвращает число, ближайшее к одному из переданных в массиве
    closestTo: function(num, bounds) {
        if(!PsIs.array(bounds) || !bounds.length) return num;//---
        var bond = bounds[0];
        var dist = Math.abs(bond-num);
        for(var i=1; i<bounds.length; i++) {
            var newBond = bounds[i];
            var newDist = Math.abs(newBond-num);
            if (dist > newDist) {
                dist = newDist;
                bond = newBond;
            }
        }
        return bond;
    },
    
    //Приводит номер к границам
    num2bounds: function(n, bounds) {
        return Math.min(Math.max(n, PsMath.min(bounds)), PsMath.max(bounds));
    },
    
    //Расстояние между точками. На вход принимается либо dx, dy, либо две точки: [x1,y1], [x2,y2]
    dist: function(dxOrP1, dyOrP2) {
        var dx = PsIs.number(dxOrP1) ? dxOrP1 : dyOrP2[0]-dxOrP1[0];
        var dy = PsIs.number(dyOrP2) ? dyOrP2 : dyOrP2[1]-dxOrP1[1];
        return Math.sqrt(PsMath.sq(dx) + PsMath.sq(dy));
    },
    
    //Границы TODO (tests)
    bounds: function(x1, x2, min, max) {
        x1 = PsIs.toNumber(x1, min);
        x2 = PsIs.toNumber(x2, max);
        
        var xl = Math.max(Math.min(x1, x2), min);
        var xr = Math.min(Math.max(x1, x2), max);
        return [xl, xr];
    },
    
    //Метод возвращает пересечение двух интервалов
    intervalIntersection: function(interval1, interval2) {
        var il = Math.max(PsMath.min(interval1), PsMath.min(interval2)); /*Common interval left*/
        var ir = Math.min(PsMath.max(interval1), PsMath.max(interval2)); /*Common interval right*/
        return il<=ir ? [il, ir] : null;
    },
    
    //"Загоняет" интервал в границы TODO (tests)
    interval2bounds: function(interval, bounds, nullIfNoIntersection) {
        if(nullIfNoIntersection && !PsMath.intervalIntersection(interval, bounds)) return null;
        
        var _n1 = PsMath.num2bounds(interval[0], bounds);
        var _n2 = PsMath.num2bounds(interval[1], bounds);
        return [_n1, _n2].sort(function(a, b) {
            return a-b;
        });
    },
    
    //НОД
    gcd: function (a) {
        var r=a[0], i,c,k;
        for(i=1;i<a.length;i++) {
            c=a[i];
            while(c!=0) {
                r-=c*Math.floor(r/c);
                k=c;
                c=r;
                r=k;
            }
        }
        return r;
    },
    
    //НОК
    lcm: function (a) {
        var s=a[0], j;
        for(j=1;j<a.length;j++) {
            s*=a[j]/PsMath.gcd([s,a[j]]);
        }
        return s;
    },
    
    //Натуральный логарифм
    ln: Math.log,
    
    //Десятичный логарифм
    lg: function(x) {
        return Math.log(x)/Math.log(10);
    },
    
    //Логарифм числа x по основанию b log_b(x)
    log: function(x, b) {
        return PsIs.number(b) ? Math.log(x)/Math.log(b) : Math.log(x);
    },
    
    
    //Размещения A_n^k
    ank: function(n, k) {
        return (n<0 || k<0 || k>n) ? 0 : PsMath.factorial(n)/PsMath.factorial(n-k);
    },
    
    //Сочетания C_n^k
    cnk: function(n, k){
        return PsMath.ank(n, k)/PsMath.factorial(k);
    },
    
    //Переводит число x из основания a в b
    base: function (x, a, b) {
        if(!b) {
            b = a;
            a = 10;
        }
        if (a==b) return x;//---
        
        var i, j, k;
        if(typeof(x)=="number") {
            j = new Array(Math.floor(PsMath.lg(x))+1);
            for (i=j.length;i>0;i--) {
                j[j.length-i]=Math.floor(x/Math.pow(10,i-1));
                x %= Math.pow(10,i-1);
            }
            x=j;
        }
        k=0;
        for(i=x.length;i>0;i--) k+=x[x.length-i]*Math.pow(a,i-1);
        x=k;
        j=new Array(Math.floor(PsMath.log(x,b))+1);
        for(i=j.length;i>0;i--) {
            j[j.length-i]=Math.floor(x/Math.pow(b,i-1));
            x %= Math.pow(b,i-1);
        }
        x=j;
        k=1;
        for(i=0;i<x.length;i++) k*=(x[i]<10);
        if(k) {
            k=0;
            for(i=x.length;i>0;i--) k+=x[x.length-i]*Math.pow(10,i-1);
            x=k;
        }
        return x;
    },
    
    /*
     * ТРИГОНОМЕТРИЯ
     */
    
    //Перевод радиан в градусы
    radToGrad: function(rad, unnormalized) {
        return PsIs.number(rad) ? PsMathCore.normalize(rad * 180/Math.PI, unnormalized) : NaN;
    },
    
    //Градусы в радианы
    gradToRad: function(grad, unnormalized) {
        return PsIs.number(grad) ? PsMathCore.normalize(grad * Math.PI/180, unnormalized) : NaN;
    },
    
    sin: function(rad) {
        return PsMathCore.normalize(Math.sin(rad));
    },
    
    cos: function(rad) {
        return PsMathCore.normalize(Math.cos(rad));
    },
    
    sinGrad: function(grad) {
        return PsMath.sin(PsMath.gradToRad(grad, true));
    },
    
    cosGrad: function(grad) {
        return PsMath.cos(PsMath.gradToRad(grad, true));
    },
    
    //Тангенс от радиан
    tg: function(rad) {
        return PsMathCore.normalize(Math.sin(rad)/PsMath.cos(rad));
    },
    
    //Тангенс от градусов
    tgGrad: function(grad) {
        return PsMath.tg(PsMath.gradToRad(grad, true));
    },
    
    //Котангенс от радиан
    ctg: function(rad) {
        return PsMathCore.normalize(Math.cos(rad)/PsMath.sin(rad));
    },
    
    //Котангенс от градусов
    ctgGrad: function(grad) {
        return PsMath.ctg(PsMath.gradToRad(grad, true));
    },
    
    //Косеканс (1/синус)
    csc: function(rad) {
        return PsMathCore.normalize(1/PsMath.sin(rad));
    },
    
    //Секанс (1/косинус)
    sec: function(rad) {
        return PsMathCore.normalize(1/PsMath.cos(rad));
    },
    
    /* 
     * 
     * TODO NOTESTS START 
     * 
     */
    
    //Обратные тригонометрические функции
    arcsin: Math.asin,
    arccos: Math.acos,
    
    arctg: function(x) {
        return Math.atan(x);
    },
    
    arcctg: function(x) {
        return Math.atan(1/x);
    },
    
    arccsc: function(x) {
        return Math.asin(1/x);
    },
    
    arcsec: function(x) {
        return Math.acos(1/x);
    },
    
    //Гиперболические функции
    sh: function(x) {
        return (Math.exp(x) - Math.exp(-x))/2;
    },
    
    ch: function(x) {
        return (Math.exp(x) + Math.exp(-x))/2;
    },
    
    th: function(x) {
        return (Math.exp(2*x) - 1)/(Math.exp(2*x) + 1);
    },
    
    cth: function(x) {
        return 1/PsMath.th(x);
    },
    
    //Обратные гиперболические функции
    arsh: function(x) {
        return Math.log(x+Math.sqrt(x*x+1))
    }, 
    
    //x>=1
    arch: function(x) {
        return Math.log(x+Math.sqrt(x*x-1));
    },
    
    arth: function(x) {
        return Math.log((1+x)/(1-x))/2;
    },
    
    arcth: function(x) {
        return PsMath.arth(1/x);
    },
    
    //Если радиус окружности идёт под углом альфа, то касательная в точке пересечения
    //радиуса с окружностью пересекает ось Ox под улгом бета. Учитывается - по или против 
    //часовой стрелки идёт поворот. Это важно для построения направления вектора.
    circBetha4Alpha: function(alpha, poCh) {
        alpha = PsIntervals.angleToCirc(alpha, 180, -180);
        var kv = PsMath.getKvadrant(alpha);
        alpha = Math.abs(alpha);
        switch (kv) {
            case 1:
                return poCh? -(90-alpha) :90+alpha;
            case 2:
                return poCh? -90+alpha : -(270-alpha);
            case 3:
                return poCh? -90-alpha : -(-90+alpha);
            case 4:
                return poCh? -(90+alpha) : 90-alpha;
        }
    },
    
    //Возвращает квадрант угла
    getKvadrant: function(grad) {
        return 1 + Math.floor(PsIntervals.angleTo0_360(grad)/90);
    },
    
    //Раскладывает угол для получения формы n*pi/2 +- alpha
    /*
     
     {
     a: оригинальный угол
     p: вариант для плюса
     {
        a: положительный угол
        n: кол-во pi/2
     }
     m: вариант для минуса
     {
        a: положительный угол
        n: кол-во pi/2
     }
     
     */
    piNa2Cnt: function(grad) {
        grad = Math.round(grad);
        
        var res = {
            'a': grad
        };
        
        $.each([-1, 1], function() {
            var _grad = grad;
            var alpha = 0;
            while (_grad%90 != 0) {
                ++alpha;
                _grad -= this;
            }
            res[this==1 ? 'p' : 'm'] = {
                n: _grad/90,
                a: alpha
            };
        });
        
        return res;
    },
    
    piNa2Str: function(grad, sign) {
        sign = PsIs.number(sign) ? (sign > 0 ? 'p' : 'm') : sign;
        
        var ob = PsMath.piNa2Cnt(grad);
        var n = ob[sign].n;
        var a = ob[sign].a;
        
        var html = '';
        if (n%2==0) {
            if (n != 0) {
                n = n/2;
                html += (n==1 ? '' : (n==-1 ? '&minus;' : n)) + PsHtmlCnst.PI;
            }
        } else {
            html += (n==1 ? '' : (n==-1 ? '&minus;' : n)) + PsHtmlCnst.PI + '/2';
        }
        
        if (a > 0) {
            switch (sign) {
                case 'p':
                    if (html) {
                        html += ' + ';
                    }
                    break;
                case 'm':
                    var min = ' &minus; ';
                    if (!html) {
                        min = $.trim(min);
                    }
                    html += min;
                    break;
            }
            html += PsHtml.mathText('&alpha;');
        }
        
        return html;
    },
    
    //Возвращает случайное число в интервале [l, r] с точностью до n знаков после запятой
    random: function (l, r, n) {
        return PsMath.round((r-l)*Math.random() + l, n);
    }
}


/**
 * Методы для работы с интервалами
 */
var PsIntervals = {
    /**
     * Создаёт интервал: массив с двумя числами - границами интервала. 
     * Если числел вообще не будет передано, вернётся null.
     * 
     * Примеры: 
     *      make([1,2], [2,3], 4) = [[1,2], [2,3], [4,4]]
     */
    make: function(a, b) {
        var intervals = PsArrays.extractSubArrays(PsUtil.functionArgs2array(arguments), function(item) {
            if(!PsIs.number(item)) return false;
            return {
                take: true,
                item: 1*item
            }
        });
        
        if(!intervals.length) return null;
        
        //Сортируем границы интервалов [[3,2], [0,1]] -> [[2,3], [0,1]], округляя их до 4 знака
        intervals.walk(function(interval) {
            //sorted.push([PsMath.round(PsMath.min(interval),4), PsMath.round(PsMath.max(interval),4)]);
            return [PsMathCore.normalize(PsMath.min(interval)), PsMathCore.normalize(PsMath.max(interval))];
        });
        
        //Сортируем интервалы попорядку [[2,3], [0,1]] -> [[0,1], [2,3]]
        //Если начало совпадает, отсортируем по длине от меньшего к большему: [[1,3], [1,2]] -> [[1,2], [1,3]]
        intervals.sort(function(i1, i2){
            var delta = i1[0] - i2[0];
            return delta!=0 ? delta : i1[1] - i2[1];
        });
        
        return intervals;
    },
    
    /**
     * Создаёт один интервал: массив с двумя числами - границами интервала.
     * Если числел вообще не будет передано, вернётся null.
     * 
     * Примеры: 
     *      make([1,2], [2,3], 4) = [1, 4]
     */
    makeSingle: function(a, b) {
        var min = null;
        var max = null;
        
        PsUtil.functionArgs2array(arguments).walk(function(item) {
            if (PsIs.number(item)) {
                min = 1 * (min===null ? item : Math.min(item, min));
                max = 1 * (max===null ? item : Math.max(item, max));
            }
        }, true);
        
        return min===null ? null : [min, max];
    },
    
    /**
     * Объединение интервалов
     * 
     * Примеры: 
     *      union([1,2], [2,3], 4) = [[1,3], [4,4]]
     */
    union: function(a, b) {
        var intervals = PsIntervals.make.apply(PsIntervals, arguments);
        
        if(!intervals) return null;
        
        //Склеиваем интервалы (старые с новыми)
        var unioned = [intervals[0]];
        for(var i=1; i<intervals.length; i++) {
            if (unioned[unioned.length-1][1]>=intervals[i][0]) {
                unioned[unioned.length-1][1] = Math.max(unioned[unioned.length-1][1], intervals[i][1]);
            } else {
                unioned.push(intervals[i]);
            }
        }
        return unioned.length ? unioned : null;
    },
    
    
    /**
     * Пересечение интервалов
     * 
     * Примеры: 
     *      intersect([1,2], [2,3]) = [2, 2]
     *      intersect([1,2], [2,3], 4) = null
     */
    intersect: function(a, b) {
        var intervals = PsIntervals.make.apply(PsIntervals, arguments);
        
        if(!intervals) return null;
        
        //Склеиваем интервалы (старые с новыми)
        var intersected = intervals[0];
        for(var i=1; i<intervals.length; i++) {
            intersected = [Math.max(intersected[0], intervals[i][0]), Math.min(intersected[1], intervals[i][1])];
            if (intersected[0]>intersected[1]) {
                return null;
            }
        }
        return intersected;
    },
    
    
    /**
     * Пересечение двух наборов интервалов
     * 
     * Примеры: 
     *      intersect2([[1,2], [3,4]], [2,3]) = [[2,2], [3,3]]
     *      intersect2([[1,3], [4,6]], [2,5]) = [[2,3], [4,5]]
     *      intersect2([[1,2], [5,6]], [3,4]) = null
     */
    intersect2: function(intervals1, intervals2) {
        if (arguments.length!=2) throw new Error('Функция intersect2 ожижает 2 параметра. Передано: ' + PsObjects.toString(arguments));
        intervals1 = PsIntervals.union(intervals1);
        if(!intervals1) return null;//---
        intervals2 = PsIntervals.union(intervals2);
        if(!intervals2) return null;//---
        
        var intersections = [];
        intervals1.walk(function(i1) {
            intervals2.walk(function(i2) {
                var isect = PsIntervals.intersect(i1, i2);
                if (isect) intersections.push(isect);
            });
        });
        return PsIntervals.union(intersections);
    },
    
    /**
     * Проверяет, входит ли переданное число или интервал в границы.
     * 
     * В качестве проверяемой сущности interval и границ bounds можно передать число, 
     * интервал или набор интервалов: 1, [1,2], [[1,2], [3,4]].
     * Все они будут преобразованы к интервалам и объединены.
     * 
     * @param {Number | Array} [interval] Проверяемая сущность
     * @param {Number | Array} [bounds] Границы вхождения
     * @return {Boolean} true, если interval входит в bounds. В противном случае - false.
     */
    isIn: function(interval, bounds) {
        if (arguments.length!=2) throw new Error('Функция isIn ожижает 2 параметра. Передано: ' + PsObjects.toString(arguments));
        interval = PsIntervals.union(interval);
        if(!interval) return false;//---
        bounds = PsIntervals.union(bounds);
        if(!bounds) return false;//---
            
            mark: for (var i=0; i<interval.length; i++) {
                for (var j=0; j<bounds.length; j++) {
                    if (interval[i][0]>=bounds[j][0] && interval[i][1]<=bounds[j][1]) {
                        continue mark;
                    }
                }
                return false;//---
            }
        
        return true;
    },
    
    //Приводит номер к границам
    numTo: function(num, bounds, defaultNum) {
        num = PsUtil.toNumber(num, defaultNum);
        if(!PsIs.number(num)) return num;//---
        bounds = PsIntervals.makeSingle(bounds);
        if(!bounds) return num;
        return Math.min(Math.max(num, bounds[0]), bounds[1]);
    },
    
    /*Функция возвращает координаты, в которых прямая пересекает границы прямоугольника*/
    line2rectangle: function(boundsX, boundsY, p1, p2, sortByY) {
        var x1 = p1[0];
        var y1 = p1[1];
        var x2 = p2[0];
        var y2 = p2[1];
        
        if(x1==x2 && y1==y2) return null;
        
        var minX = PsMath.min(boundsX);
        var maxX = PsMath.max(boundsX);
        
        var minY = PsMath.min(boundsY);
        var maxY = PsMath.max(boundsY);
        
        var lim = null, unlim = null, bounds;
        if (x1==x2) {
            if(!PsIntervals.isIn(x1, boundsX)) return null;
            //Если линия безгранична, то она пересекает прямоугольник на его границах
            unlim = [[x1, minY], [x1, maxY]];
            //Если линия ограничена, найдём точки пересечения
            bounds = PsIntervals.intersect([y1, y2], boundsY);
            if (bounds) lim = [[x1, bounds[0]], [x1, bounds[1]]];
        } else if(y1==y2) {
            if(!PsIntervals.isIn(y1, boundsY)) return null;
            //Если линия безгранична, то она пересекает прямоугольник на его границах
            unlim = [[minX, y1], [maxX, y1]];
            //Если линия ограничена, найдём точки пересечения
            bounds = PsIntervals.intersect([x1, x2], boundsX);
            if (bounds) lim = [[bounds[0], y1], [bounds[1], y1]];
        } else {
            var K = (y2-y1)/(x2-x1);
            
            var y = function(x){
                return K*(x-x1) + y1;
            }
            var x = function(y) {
                return (y-y1)/K + x1;
            }
            
            bounds = PsIntervals.intersect([x(minY), x(maxY)], boundsX);
            
            if (!bounds) return null; //Безграничаня линия не пересекает прямоугольник
            
            var x1u = bounds[0]; /*u - unlimited*/
            var y1u = y(x1u);
            
            var x2u = bounds[1];
            var y2u = y(x2u);
            
            unlim = [[x1u, y1u], [x2u, y2u]];
            
            bounds = PsIntervals.intersect([x1, x2], [x1u, x2u]);
            if (bounds) lim = [[bounds[0], y(bounds[0])], [bounds[1], y(bounds[1])]];
        }
        
        if (sortByY) {
            lim = lim ? lim.sort(function(p1, p2){
                return p1[1] - p2[1];
            }) : null;
            
            unlim = unlim ? unlim.sort(function(p1, p2){
                return p1[1] - p2[1];
            }) : null;
        }
        
        return {
            lim: lim,
            unlim: unlim
        };
    },
    
    /**
     * Приводит угол в градусах в заданный интервал длиной 360 градусов:
     * [0, 360), (-360, 0], (-180, 180] и т.д.
     * 
     * Примеры: 
     *      angleToCirc(-30, 0, 360) = 330
     *      angleToCirc(180, 90, -270) = -180
     */    
    angleToCirc: function(grad, include, exclude) {
        if (Math.abs(include-exclude)!=360) throw new Error('Invalid angleToCirc interval: ['+include+', '+exclude+']');
        var min = Math.min(include, exclude);
        var max = Math.max(include, exclude);
        while (grad > max || grad < min || grad==exclude) {
            grad += grad >= max ? -360 : 360;
        }
        return grad;//---
    },
    
    /**
     * Приводит угол в градусах к интервалу [0, 360).
     * 
     * Примеры: 
     *      angleTo0_360(0) = 0
     *      angleTo0_360(360) = 0
     *      angleTo0_360(90) = 90
     *      angleTo0_360(450) = 90
     */    
    angleTo0_360: function(grad) {
        return PsIntervals.angleToCirc(grad, 0, 360);
    }
}

/**
 * Менеджер для вычисления математических выражений
 */
var PsMathEval = {
    //Версия класса, для сброса кешей
    ver: 1.0,
    //Экземпляр класса, выполняющего eval
    evaluator: null,
    //Максимальная дельта для расчётов ООФ
    MAX_CALC_DELTA: 0.1,
    //Минимальная точность определения асимптоты
    MIN_CALC_DELTA: PsMathCore.MIN_NUMBER,
    //Дельта для расчётов ООФ поумолчанию
    DFLT_CALC_DELTA: 0.1,
    //Минимальное кол-во проверок одного интервала на наличие асимптот
    DELTA_CHECKS_MIN: 6,
    //Инициализация класса
    init: function() {
        /**
         * Функция-синглтон, в контексте которой можно производить eval математических выражений.
         */
        this.evaluator = new function PsMathEvaluator() {
            //Константы
            var e =  Math.E;
            var pi = Math.PI;
            
            //Math
            var abs = Math.abs;
            var pow = Math.pow;
            var exp = Math.exp;
            var sqrt = Math.sqrt;
            var floor = Math.floor;
            var ceil = Math.ceil;
            
            //PsMath
            for (var v in PsMath) {
                if (v.startsWith('_')) continue;
                //Внесём в контекст данной функции все функции из PsMath
                eval('var '+v+' = PsMath.'+v+';');
            }
            
            this.eval = function(expression, _VALUE_OF_ARGUMENT_) {
                var x = _VALUE_OF_ARGUMENT_;
                var y = _VALUE_OF_ARGUMENT_;
                return eval(expression);
            }
        };
    },
    
    eval: function(expression, x) {
        return this.evaluator.eval(expression, x);
    },
    
    evalSave: function(ex, x) {
        try {
            var res = this.eval(ex, x);
            return PsIs.number(res) ? res : null;
        }
        catch (e) { 
            return null;
        }
    },
    
    /**
     * Методу на вход передаётся асимптота - левая или правая, а он вычисляет границу этой асимптоты.
     */
    calcAsymp: function(lx, ly, rx, ry, doEval) {
        assert((ly===null && ry!==null) || (ly!==null && ry===null), 'Invalid calcAsymp arguments');
        var hasLeft = ry === null; //Признак того, что асимптота справа [lx null]
        
        var dx = PsMathCore.MIN_NUMBER;
        var ret = hasLeft ? lx : rx;
        
        //Сначала определим знак функции на той границе, на которой асимптоты нет
        var validYsign = PsMath.sign(hasLeft ? ly : ry);
        if (validYsign === 0) {
            //Если знак равен нулю, то сделаем шажок в сторону и определим таки знак
            var validYdx = doEval(hasLeft ? lx + dx : rx - dx);
            if (validYdx === null) return ret;  //Асимптота в шаге от границы
            validYsign = PsMath.sign(validYdx); //Берём знак, даже если 0
        }
        
        //Определим шаг расчёта и checkX, с которого будем начинать
        var step = PsMath.normalize((rx-lx)/2);
        assert(step > dx, 'Init step ('+step+') <= min step ('+dx+')')
        var checkX = PsMath.normalize(lx + step);
        var lastY;      //Последний расчитанный y
        var moveTo = 0; //Направление движения: -1 - влево, +1 - вправо

        while ((lx < checkX) && (checkX < rx))  {
            //Посмотрим, куда "Попали"
            lastY = doEval(checkX);
            
            //Проанализируем
            if ((null === lastY) || (validYsign!=PsMath.sign(lastY))) {
                //checkX - попал на асимптоту
                //Если асимптота справа, надо двигаться влево и наоборот
                moveTo = hasLeft ? -1 : 1;
            } else {
                //checkX - попал в ООФ
                ret = checkX;
                //Если асимптота справа, надо двигаться вправо и наоборот
                moveTo = hasLeft ? 1 : -1;
            }
            
            //Проверять будем перед уменьшением step, чтобы последний шаг сделать с dx
            if (step <= dx) break;//---
            
            //Уменьшим интервал вдвое
            step = Math.max(PsMath.normalize(step/2), dx);

            //Сдвигаем checkX
            checkX = PsMath.normalize(checkX + (moveTo * step));
        }
        
        return ret;
    },
    
    //Определяет, есть ли асимптота между двумя точками и, если есть, вычисляет её границы
    calcAsympMiddle: function(lX, lY, rX, rY, doEval) {
        //Вычисляем границы по y
        var minY = Math.min(lY, rY);
        var maxY = Math.max(lY, rY);
        
        //Инициализируем нужные переменные
        var dx = PsMathCore.MIN_NUMBER;
        var step = PsMath.normalize((rX-lX)/2);
        assert(step > dx, 'Init step ('+step+') <= min step ('+dx+')')
        var checkX = PsMath.normalize(lX + step);
        var checkY = null;
        var found = false;
        var moveTo = 0; //Направление движения: -1 - влево, +1 - вправо
        var check = 0;
        
        var moveDirection = function(left, right) {
            return left == right ? 0 : (Math.abs(left) < Math.abs(right) ? 1 : -1);
        }
        
        while ((lX < checkX) && (checkX < rX)) {
            checkY = doEval(checkX);
            if (checkY === null) {
                found  = true
                break;
            }

            //Проверять будем перед уменьшением step, чтобы последний шаг сделать с dx
            if (step <= dx) break;//---

            //Значение не выбивается за рамки, скорее всего всё хорошо
            if (++check >= this.DELTA_CHECKS_MIN && minY <= checkY && checkY <= maxY) return null;//---
            //if (minY <= checkY && checkY <= maxY) return null;//---
            
            //Определим направление, в котором функция растёт, и сдвинемся в том направлении
            var dxLeft = PsMath.normalize(checkX - dx);
            var dyLeft = doEval(dxLeft);
            if (dyLeft === null) {
                checkX = dxLeft;
                found  = true
                break;
            }
                
            var dxRight = PsMath.normalize(checkX + dx);
            var dyRight = doEval(dxRight);
            if (dyRight === null) {
                checkX = dxRight;
                found  = true
                break;
            }
                
            moveTo = moveDirection(dyLeft, dyRight);
            //Проверим на одинаковость значений (перевал через экстремум)
            if (moveTo == 0) return null;

            //Уменьшим интервал вдвое
            step = Math.max(PsMath.normalize(step/2), dx);
            
            //Сдвигаем checkX
            checkX = PsMath.normalize(checkX + (moveTo * step));
        }
        
        if (found) {
            return {
                l: this.calcAsymp(lX, lY, checkX, null, doEval), 
                r: this.calcAsymp(checkX, null, rX, rY, doEval)
            }
        }
        
        return null;
    },
        
    //Собирает информацию о предстоящем расчёте ООФ
    gatherCalcInfo: function(expression, bounds, dx) {
        
        var obj = {
            ex: null,
            dx:  null,      //Шаг расчёта
            dxUser: false,  //Шаг расчёта всят тот, который передан извне
            acc: null,      //Ночность шага расчёта
            bounds: null,   //Границы расчёта
            estimateCalcs: null,
            error: null     //Причина, по которой расчёт не может быть совершён
        };
        
        var gather = function() {
            if (!PsIs.string(expression) || PsIs.empty(expression)) {
                return 'Некорректное выражение для расчёта ООФ: ' + PsObjects.toString(expression);
            }
            obj.ex = PsStrings.trim(expression);
            
            //Сразу фетчим интервал расчёта:
            obj.dx = PsIntervals.numTo(dx, [this.MIN_CALC_DELTA, this.MAX_CALC_DELTA], this.DFLT_CALC_DELTA);
            assert(PsIs.decimal(obj.dx) && obj.dx>0, 'Invalid dx: ' + dx + ' converted to ' + obj.dx);
            obj.dxUser = obj.dx==dx;
            
            //Проверим точность
            obj.acc = PsMathCore.decimalAccuracy(obj.dx);
            assert(obj.acc>0, 'Точности шага расчёта ООФ недостаточно. Шаг: ' + obj.dx + ', точность: ' + obj.acc);

            //Границы расчёта
            obj.bounds = PsIntervals.makeSingle(bounds);
            if (!obj.bounds || (obj.bounds[1] - obj.bounds[0]<3*obj.dx)) {
                return 'Некорректные границы расчёта ООФ: ' + PsObjects.toString(bounds) + (bounds ? ' при шаге расчёта '+obj.dx : '');
            }
            
            /*
             * Оценочное кол-во операций расёта. Будет выполнено:
             * 1. evalMandatory расчётов обязательно
             * 2. на каждый расчёт ещё this.DELTA_CHECKS_MIN подрасчётов (центр отрезка)
             * 3. На все, кроме последнего подрасчёта, будет сделано ещё два расчёта для определения направления сдвига
             */
            /*
            var evalMandatory = Math.round((obj.bounds[1]-obj.bounds[0])/obj.dx);
            var evalAddon = evalMandatory * (this.DELTA_CHECKS_MIN + (this.DELTA_CHECKS_MIN-1) * 2);
            obj.estimateCalcs = evalMandatory + evalAddon;
            */
            obj.estimateCalcs = Math.round((obj.bounds[1]-obj.bounds[0])/obj.dx);
            
            return null;
        }
        
        //Присвоим ошибку
        obj.error = gather.call(this);
        
        return obj;//---
    },
    
    /**
     * Функция, вычисляющая область определения функции на заданном интервале.
     * Данная функция не производит никаких кеширований, тупо берёт и вычисляет ООФ 
     * на заданном отрезке.
     */
    calcFuncDefInterval: function(expression, bounds, dx, progressWatcher) {
        //Собираем информацию о предстоящем расчёте
        var calcInfo = this.gatherCalcInfo(expression, bounds, dx);
        
        //Проверим, можно ли с этими параметрами провести вычисление
        if (calcInfo.error) throw new Error(calcInfo.error);

        //Переопределим параметры вызова
        dx = calcInfo.dx;
        bounds = calcInfo.bounds;
        expression = calcInfo.ex;
        
        //Кол-во значащих цифр после запятой
        var acc = calcInfo.acc;
        var useAsymp = dx > this.MIN_CALC_DELTA;
        
        var round = function(x) {
            return PsMath.round(x, acc);
        }
        
        var evals = 0;
        var doEval = function(x) {
            ++evals;
            return PsMathEval.evalSave(expression, x);
        }
        
        var boundsL = bounds[0];
        var boundsR = bounds[1];
        
        var stepsMade  = 0;
        var stepsTotal = calcInfo.estimateCalcs;
        var watchSteps = PsIs.func(progressWatcher);
        var logPortion = Math.max(Math.min(Math.round(stepsTotal/4), 5000), 50);
        var logPortionNum, logPortionLastNum = -1;

        //Подготовим параметры для сбора результата
        var intervals = [];
        
        //Подготовим параметры цикла и запустим его
        var x, y, asymp, lastX = null, lastY = null, startX = null, doBreak = false;
        for(x = boundsL; ; x+=dx) {
            if (doBreak) {
                break;//---
            }
            
            //Не первый шаг
            if (lastX!==null) {
                //Если шаг первый или последний, то не будем выполнять округление
                if (x>= boundsR) {
                    x = boundsR;
                    doBreak = true;
                } else {
                    //Шаг не первый и не последний - округляем
                    x = round(x);
                }
                
                //Защитимся от того, что второй или последний x без округления может совпасть с lastX с округлением
                if (x===lastX) continue;//---
            }
            
            //Посчитаем прогресс и оповестим слушателей
            watchSteps = watchSteps && (stepsMade<=stepsTotal) && ((stepsTotal - stepsMade) >= logPortion);
            if (watchSteps) {
                logPortionNum = Math.floor(stepsMade/logPortion);
                if (logPortionLastNum!= logPortionNum) {
                    logPortionLastNum = logPortionNum;
                    progressWatcher(stepsTotal, logPortionNum * logPortion);
                }
                
                ++stepsMade;
            }
            
            y = doEval(x);
            
            if (y===null) {
                if (lastX === null) {
                    //Первая же точка - и сразу провал
                    lastX = x;
                    continue;//---
                }
                if (startX === null) {
                    //Точки уже были, но предыдущая точка не входит в ООФ
                    lastX = x;
                    continue;//---
                }
                //Предыдущая точка входит в ООФ, а значит lastY!=null - асимптота справа
                assert(lastY !== null, 'lastY cannot be null here')
                
                //Вычислим асимптоту справа для последней точки: [lastX null]
                if (useAsymp) {
                    asymp = this.calcAsymp(lastX, lastY, x, null, doEval);
                } else {
                    asymp = lastX;
                }
                
                //Проверим асимптоту
                assert(asymp >= lastX && asymp < x, 'asymp >= lastX && asymp < x')
                
                //Если интервал начался с асимптоты, то asymp может совпасть с startX
                if (startX < asymp) {
                    intervals.push([startX, asymp]);
                }
                
                startX = null;
                lastX = x;
                lastY = null;
                
                continue;//---
            }
            
            //Если не начат, то начинаем новый интервал
            if (startX === null) {
                assert(lastY === null, 'lastY must be null here')
                if (lastX === null) {
                    //x - первая точка расчёта, начинаем интервал с неё
                    startX = x;
                } else {
                    //В предыдущей точке была асимптота, возмём её край в качестве начала интервала: [null x]
                    if (useAsymp) {
                        asymp = this.calcAsymp(lastX, null, x, y, doEval);
                    } else {
                        asymp = x;
                    }
                    
                    //Проверим асимптоту
                    assert(asymp > lastX && asymp <= x, 'asymp > lastX && asymp <= x')
                    
                    startX = asymp;
                }
                lastX = x;
                lastY = y;
                continue;//---
            }
            
            assert(lastX!==null && lastY !== null, 'lastX!==null && lastY !== null')
            //В этой точке у нас вылидные y и lastY. Убедимся, что между x и lastX нет асимптоты
            
            if (useAsymp) {
                asymp = this.calcAsympMiddle(lastX, lastY, x, y, doEval);
                if (asymp) {
                    //Проверим валидность расположения асимптот
                    assert(asymp.l < asymp.r, 'asymp.l < asymp.r')
                
                    /*
                     * Проверим левую асимптоту.
                     * Интервал был начат минимум на предыдущем щаге, а значит, если есть асимптота, то она позже начала интервала
                     */
                    assert((lastX <= asymp.l) && (asymp.l < x), '(lastX <= asymp.l) && (asymp.l < x)')
                    intervals.push([startX, asymp.l]);
                
                    assert((lastX < asymp.r) && (asymp.r <= x), '(lastX < asymp.r) && (asymp.r <= x)')
                    startX = asymp.r;
                }
            }
            
            lastX = x;
            lastY = y;
        }
        
        //Проверим последнюю вычисленную точку
        if (startX!==null && (startX < lastX)) {
            intervals.push([startX, lastX]);
        }
        
        if (intervals.length > 0) {
            assert(intervals[0][0]>=boundsL, 'First interval >= boundsL');
            assert(intervals[intervals.length-1][1]<=boundsR, 'Last interval <= boundsR');
        }
        
        //Оповещаем слушателя об изменении прогресса
        if (PsIs.func(progressWatcher)) progressWatcher(stepsTotal, stepsTotal);
        
        return {
            df: intervals,
            evals: evals
        }
    }
}

PsMathEval.init();