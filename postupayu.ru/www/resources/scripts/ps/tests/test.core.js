document.getElementById('ps-jq-state').setAttribute('class', PsCore.hasJquery ? 'enabled' : '');
document.getElementById('ps-jq-state').innerHTML = PsCore.hasJquery ? 'jQuery ON' : 'jQuery OFF';

/*********************/
QUnit.module('PsCore');

QUnit.asyncTest('startTime', function() {
    var startTime = PsCore.startTime;
    expect(4);
    ok(PsIs.integer(PsCore.startTime), PsCore.startTime + ' is integer');
    ok(PsCore.startTime > new Date().getTime() - 60 * 1000, 'More then munute before');
    ok(PsCore.startTime < new Date().getTime() + 1, 'Less then cur date');
    setTimeout(function() {
        deepEqual(startTime, PsCore.startTime, 'Is constant');
        QUnit.start();
    }, 50);
});

QUnit.test('hasWindow', function() {
    ok(PsCore.hasWindow===true, 'Is true');
});

QUnit.test('hasJquery', function() {
    deepEqual(PsCore.hasJquery, !!window['jQuery'], 'PsCore.hasJquery='+PsCore.hasJquery);
});

QUnit.test('hasLocalStorage', function() {
    deepEqual(PsCore.hasLocalStorage, !!window['localStorage'], 'PsCore.hasLocalStorage='+PsCore.hasLocalStorage);
});

QUnit.test('hasConsole', function() {
    deepEqual(PsCore.hasConsole, !!window['console'], 'PsCore.hasConsole='+PsCore.hasConsole);
});

QUnit.test('hasWorker', function() {
    deepEqual(PsCore.hasWorker, !!window['Worker'], 'PsCore.hasConsole='+PsCore.hasConsole);
});

QUnit.test('type', function() {
    var doTestType = function(ob, typeExpected) {
        var typeActual = PsCore.type(ob);
        deepEqual(typeActual, typeExpected, 'PsCore.type('+{}.toString.apply(ob)+')='+typeActual+', expected: '+typeExpected);
    }
    
    //"Boolean Number String Function Array Date RegExp Object Error"
    doTestType(null, 'null');
    doTestType(undefined, 'undefined');
    doTestType(true, 'boolean');
    doTestType(false, 'boolean');
    doTestType(1, 'number');
    doTestType(-2, 'number');
    doTestType(1.1, 'number');
    doTestType(-1.2, 'number');
    doTestType('', 'string');
    doTestType('a', 'string');
    doTestType(' ', 'string');
    doTestType(doTestType, 'function');
    doTestType(function() {}, 'function');
    doTestType([], 'array');
    doTestType([1], 'array');
    doTestType(new Date(), 'date');
    doTestType(new RegExp(), 'regexp');
    doTestType(/ds/, 'regexp');
    doTestType({}, 'object');
    doTestType(new Error('xxx'), 'error');
});

QUnit.test('isType', function() {
    var doTestType = function(ob, typeExpected) {
        var isType = PsCore.isType(ob, typeExpected);
        ok(isType, 'PsCore.isType('+{}.toString.apply(ob)+', '+typeExpected+')='+isType);
    }
    
    //"Boolean Number String Function Array Date RegExp Object Error"
    doTestType(null, 'null');
    doTestType(undefined, 'undefined');
    doTestType(true, 'boolean');
    doTestType(false, 'boolean');
    doTestType(1, 'number');
    doTestType(-2, 'number');
    doTestType(1.1, 'number');
    doTestType(-1.2, 'number');
    doTestType('', 'string');
    doTestType('a', 'string');
    doTestType(' ', 'string');
    doTestType(doTestType, 'function');
    doTestType(function() {}, 'function');
    doTestType([], 'array');
    doTestType([1], 'array');
    doTestType(new Date(), 'date');
    doTestType(new RegExp(), 'regexp');
    doTestType(/ds/, 'regexp');
    doTestType({}, 'object');
    doTestType(new Error('xxx'), 'error');
});


QUnit.test('EN_ALPHABET', function() {
    deepEqual(typeof(PsCore.EN_ALPHABET), 'string');
    deepEqual(PsCore.EN_ALPHABET.length, 26);
    deepEqual(PsCore.EN_ALPHABET, PsCore.EN_ALPHABET.toLowerCase());
});

QUnit.test('EN_ALPHABET_NUM', function() {
    deepEqual(typeof(PsCore.EN_ALPHABET_NUM), 'string');
    deepEqual(PsCore.EN_ALPHABET_NUM.length, 26+10);
    deepEqual(PsCore.EN_ALPHABET_NUM, PsCore.EN_ALPHABET_NUM.toLowerCase());
    deepEqual(PsCore.EN_ALPHABET_NUM, PsCore.EN_ALPHABET+'0123456789');
});

QUnit.test('CALC_ACCURACY', function() {
    deepEqual(typeof(PsCore.CALC_ACCURACY), 'number');
    deepEqual(parseInt(Number(PsCore.CALC_ACCURACY), 10), PsCore.CALC_ACCURACY);
    ok(PsCore.CALC_ACCURACY>2);
    ok(PsCore.CALC_ACCURACY<20);
});

/********************/
QUnit.module('PsIs');

var doTestPsIs = function(fname, trueArr, falseArr) {
    if (fname=='arguments') {
        trueArr.push(arguments);
    }
    QUnit.test(fname, function() {
        var func = PsIs[fname], testVal;
        if (trueArr) {
            for (var i=0; i<trueArr.length; i++) {
                testVal = trueArr[i];
                ok(func(testVal)===true, 'PsIs.'+fname+'('+Object.prototype.toString.call(testVal)+')===true');
            }
        }
        if (falseArr) {
            for (var j=0; j<falseArr.length; j++) {
                testVal = falseArr[j];
                ok(func(testVal)===false, 'PsIs.'+fname+'('+Object.prototype.toString.call(testVal)+')===false');
            }
        }
    });
}

doTestPsIs('array', [[]], [{}, 1, '', null, undefined, 'xxx', '[]', false, true, doTestPsIs, NaN]);
doTestPsIs('func', [doTestPsIs], [{}, 1, '', null, undefined, 'xxx', '[]', false, true, [], NaN]);
doTestPsIs('defined', [doTestPsIs, {}, 1, '', null, 'xxx', '[]', false, true, [], NaN], [undefined]);
doTestPsIs('string', ['', 'xx'], [doTestPsIs, {}, 1, null, undefined, false, true, [], NaN]);
doTestPsIs('arguments', [], ['', 'xx', doTestPsIs, {}, 1, null, undefined, false, true, [], NaN]);
doTestPsIs('object', [{}, []], [doTestPsIs, 1, null, undefined, false, true, '', '{}', NaN]);
doTestPsIs('error', [new Error('xxxxx')], [{}, [], doTestPsIs, 1, 1.1, null, undefined, false, true, '', '{}', NaN, 'Error']);
doTestPsIs('number', [1, -1.2, '3', '-1.7'], [doTestPsIs, null, undefined, false, true, '', '{}', {}, [], Number.POSITIVE_INFINITY, Number.NEGATIVE_INFINITY, NaN, [1], [1,2,3]]);
doTestPsIs('integer', [1, '3', '4e2', -1, '-4'], [-1.2, '-1.7', doTestPsIs, null, undefined, false, true, '', '{}', {}, [], Number.POSITIVE_INFINITY, Number.NEGATIVE_INFINITY, NaN]);
doTestPsIs('exponent', [Number.MAX_VALUE, Number.MIN_VALUE, 7.123.toExponential(2), '1e+1', '1E+1', 99999999999999999999999999, -888888888888888888888888], ['1a+1', '1B+1', 1, 1.2, -4, -1.2, '-1.7', doTestPsIs, null, undefined, false, true, '', '{}', {}, [], Number.POSITIVE_INFINITY, Number.NEGATIVE_INFINITY, NaN]);
doTestPsIs('decimal', [1.1, '3.1', -3.1, '-2.1'], [-1, '-1', 2, '3', doTestPsIs, null, undefined, false, true, '', '{}', {}, [], Number.POSITIVE_INFINITY, Number.NEGATIVE_INFINITY, NaN]);
if (PsCore.hasJquery) {
    doTestPsIs('jQuery', [$('.xxx-xxx')], [1, '3', -1.2, '-1.7', doTestPsIs, null, undefined, false, true, '', '{}', {}, [], NaN]);
    doTestPsIs('empty', [$('.xxx-xxx'), [], 0, false, null, undefined, '', ' ', '   ', NaN], [1, '3', -1.2, '-1.7', doTestPsIs, true, '{}', {}, $('#qunit')]);
} else {
    doTestPsIs('jQuery', [], [1, '3', -1.2, '-1.7', doTestPsIs, null, undefined, false, true, '', '{}', {}, [], NaN]);
    doTestPsIs('empty', [[], 0, false, null, undefined, '', ' ', '   ', NaN], [1, '3', -1.2, '-1.7', doTestPsIs, true, '{}', {}]);
}


/********************/
QUnit.module('PsUtil');

QUnit.test('trim', function() {
    var tests = [['a', 'a'], [null, ''], [false, 'false'], [1, '1'], [' a', 'a'], ['a ', 'a'], [' a ', 'a']];
    for (var i=0; i<tests.length; i++) {
        var notTrimmed = tests[i][0];
        var trimmed = tests[i][1];
        ok(PsUtil.trim(notTrimmed) === trimmed, 'PsUtil.trim("'+notTrimmed+'")="'+trimmed+'"');
    }
});

QUnit.test('ms2s', function() {
    var tests = [[1003, 1], [999, 1], [1999, 2], [2005, 2]];
    for (var i=0; i<tests.length; i++) {
        var msec = tests[i][0];
        var sec = tests[i][1];
        deepEqual(PsUtil.ms2s(msec), sec, 'PsUtil.ms2s('+msec+')='+sec);
        deepEqual(PsUtil.ms2s(msec, true), msec, 'PsUtil.ms2s('+msec+', true)='+msec);
    }
});

QUnit.test('functionArgs2array', function() {
    var tests = [[], [1003, 1], ['a', 'b'], [null, -1], [undefined, undefined, undefined]];
    var params = [];
    var callback = function() {
        deepEqual(PsUtil.functionArgs2array(arguments), params, 'callback('+params+')');
    }
    for (var i=0; i<tests.length; i++) {
        params = tests[i];
        callback.apply(null, params);
    }
});

QUnit.test('nextInt', function() {
    var testNextInt = function(min, max) {
        var rnd = PsUtil.nextInt(min, max);
        var call = min+ ' <= PsUtil.nextInt('+min+', '+max+')='+rnd+' <= ' + max;
        ok(PsIs.integer(rnd) && rnd<=max && rnd>=min, call);
    }
    
    testNextInt(1, 1);
    testNextInt(1, 2);
    testNextInt(1, 3);
    testNextInt(1, 100);
    testNextInt(-100, 200);
    
    var obj = {
        1: 0,
        2: 0,
        3: 0
    }
    var normal = 280;
    for (var i=0; i<1000; i++) {
        ++obj[PsUtil.nextInt(1, 3)];
    }
    
    ok(obj[1]>=normal && obj[2]>=normal && obj[3]>=normal, 'Normal distribution: '+obj[1]+', '+obj[2]+', '+obj[3]);
});


QUnit.asyncTest('startTimerOnce', function() {
    
    var tests = 200;
    var executed = 0;
    expect(tests*2 + 1);
    
    var doTest = function(n) {
        var ctxt = 'CTXT['+n+']';
        var delay = PsUtil.nextInt(300, 1000);
        var waited = new Date().getTime();
        var callback = function() {
            waited = new Date().getTime() - waited;
            ++executed;
            ok(this==ctxt, '#'+n+'. Ctxt check: '+this+'=='+ctxt);
            ok(waited>=delay, '#'+n+'. Waited ('+waited+') >= delay ('+delay+')');
        }
        PsUtil.startTimerOnce(callback, delay, ctxt);
    }
    
    
    for(var i=1; i<=tests; i++) {
        doTest(i);
    }
    
    PsUtil.startTimerOnce(function() {
        ok(tests == executed, 'Executed('+executed+'/'+tests+')');
        QUnit.start();
    }, 2000);
});


QUnit.test('hasGlobalObject', function() {
    ok(PsUtil.hasGlobalObject('QUnit')===true, 'QUnit');
    ok(PsUtil.hasGlobalObject('NotExistedObject!!!')===false, 'NotExistedObject');
});

QUnit.test('callGlobalObject', 1, function() {
    PsUtil.callGlobalObject('QUnit', function() {
        ok(this===QUnit, 'QUnit ctxt');
    });
});


QUnit.test('toNumber', function() {
    ok(PsUtil.toNumber('1')===1, '1');
    ok(PsUtil.toNumber('1.23')===1.23, '1.23');
    ok(PsUtil.toNumber('a', 3)===3, '3');
    ok(PsUtil.toNumber('a', 'b')===null, 'null');
    ok(PsUtil.toNumber('-1', 'b')===-1, '-1');
});


QUnit.test('toInteger', function() {
    ok(PsUtil.toInteger('1')===1);
    ok(PsUtil.toInteger('1.23')===null);
    ok(PsUtil.toInteger('a', 3)===3);
    ok(PsUtil.toInteger('a', 'b')===null);
    ok(PsUtil.toInteger('-1', 'b')===-1);
    ok(PsUtil.toInteger('a', Number.MAX_VALUE)===null);
    ok(PsUtil.toInteger('a', Number.MIN_VALUE)===null);
});

QUnit.test('round', function() {
    var doTest = function(x, n, expected) {
        var actual = PsUtil.round(x, n);
        deepEqual(actual, expected, 'PsUtil.round('+x+', '+n+')='+actual);
    }
    
    
    doTest(1, 2, 1);
    doTest(0.1, null, 0);
    doTest(0.5, null, 1);
    doTest(1.2, 2, 1.2);
    doTest(1.23, 2, 1.23);
    doTest(1.234, 2, 1.23);
    doTest(-1.234, 1, -1.2);
    doTest(-1.234, 2, -1.23);
    doTest(-1.234, 3, -1.234);
    doTest(1.4999999, 3, 1.5);
    doTest(1.4991, 3, 1.499);
    doTest(1.4000001, 3, 1.4);
    doTest(1.4009001, 3, 1.401);
    doTest(1.4009001, 0, 1);
    doTest(1.6009001, 0, 2);
    doTest(1.4009001, -3, 1);
    doTest(1.6009001, -3, 2);
    doTest(1.4009001, 1, 1.4);
    doTest(1.6009001, 1, 1.6);
    
    for (var i=0; i<1000; i++) {
        //Проверим, что на больших числах всегда одинаковый результат
        doTest(1.23456789123456789, 6, 1.234568);
    }
    
    for (var j=0; j<1000; j++) {
        //Проверим, что на больших числах всегда одинаковый результат
        doTest(-1.23456789123456789, 6, -1.234568);
    }
    
    //Проверим с двумя цифрами в конце самого маленького числа
    var minLess2 = '0.' + PsStrings.padRight('', '0', PsMathCore.CALC_ACCURACY-2);
    for(var n=0; n<=9; n++) {
        doTest(minLess2+'0'+n, PsMathCore.CALC_ACCURACY-1, n<5? 0:1*(minLess2+1));
    }
});

QUnit.asyncTest('getUpTime', function() {
    expect(4);
    
    var minUpTimeMsec = 1000;
    var minUpTimeSec =  PsUtil.ms2s(minUpTimeMsec);
    
    PsUtil.startTimerOnce(function() {
        var upTimeSec = PsUtil.getUpTime(true);
        var upTimeMsec = PsUtil.getUpTime();
        
        ok(PsIs.integer(upTimeSec), 'upTimeSec('+upTimeSec+') is integer');
        ok(upTimeSec>=minUpTimeSec, 'upTimeSec('+upTimeSec+') >= ' + minUpTimeSec);
        ok(PsIs.integer(upTimeMsec), 'upTimeMsec('+upTimeMsec+') is integer');
        ok(upTimeMsec>minUpTimeMsec, 'upTimeMsec('+upTimeMsec+') > '+minUpTimeMsec);
        
        QUnit.start();
    }, minUpTimeMsec + 1);

});

QUnit.asyncTest('getStartTime', function() {
    expect(4);
    
    var startTimeMsec = PsUtil.getStartTime();
    var startTimeSec = PsUtil.getStartTime(true);
    
    PsUtil.startTimerOnce(function() {
        
        var maxStartTimeMsec = new Date().getTime();
        var maxStartTimeSec =  PsUtil.ms2s(maxStartTimeMsec);
        
        ok(PsIs.integer(startTimeMsec), 'startTimeMsec('+startTimeMsec+') is integer');
        ok(startTimeMsec<maxStartTimeMsec, 'startTimeMsec('+startTimeMsec+') < ' + maxStartTimeMsec);
        
        ok(PsIs.integer(startTimeSec), 'startTimeSec('+startTimeSec+') is integer');
        ok(startTimeSec<=maxStartTimeSec, 'startTimeSec('+startTimeSec+') <= '+maxStartTimeSec);
        
        QUnit.start();
    }, 20);

});


QUnit.asyncTest('executeOnUptime', function() {
    expect(2);
    
    //Тест выполнится только если аптайм больше 1 секунды
    var startTest = function() {
        var waited = new Date().getTime();
        var ctxt = 'CTXT';
        PsUtil.executeOnUptime(1000, function() {
            waited = new Date().getTime() - waited;
            
            ok(waited<=50, 'waited('+waited+')<=100');
            ok(this==ctxt, 'this('+this+') == ctxt('+ctxt+')');
            
            QUnit.start();
        }, ctxt);
    }
    
    if (PsUtil.getStartTime()>=1000) {
        startTest();
    } else {
        PsUtil.startTimerOnce(startTest, 1001);
    }

});


QUnit.test('once', function() {
    expect(11);
    
    var ctxt = 'CTXT';
    var called = 0;
    var callback = PsUtil.once(function() {
        ok(this==ctxt, 'this('+this+') == ctxt('+ctxt+')');
        return ++called;
    }, ctxt);
    
    ok(callback()==1, 'Returned same result: ' + callback());
    ok(callback()==1, 'Returned same result: ' + callback());
    ok(callback()==1, 'Returned same result: ' + callback());
    ok(called==1, 'Called('+called+') only once');
    
    callback = PsUtil.once(function() {
        ok(this==ctxt, 'this('+this+') == ctxt('+ctxt+')');
        return ++called;
    });
    
    ok(callback.call(ctxt)==2, 'Returned same result: ' + callback());
    ok(callback()==2, 'Returned same result: ' + callback());
    ok(callback()==2, 'Returned same result: ' + callback());
    ok(called==2, 'Called('+called+') only once');
    
    callback = PsUtil.once(null);
    ok(PsIs.func(callback), 'Callback is function when null');
});


QUnit.test('safeCall', function() {
    expect(10);
    
    var ctxt = 'CTXT';
    var func = function() {
        deepEqual(this, ctxt, 'ctxt='+this);
    };
    
    [null, 0, undefined, {}, function(){},func].walk(function(item) {
        var actual = PsUtil.safeCall(item, ctxt);
        ok(PsIs.func(actual), 'PsUtil.safeCall('+item+')='+actual);
        actual();
    });
    
    func = function(a,b,c) {
        return this + a+b+c;
    };
    func = PsUtil.safeCall(func, 10);
    deepEqual(func(1,2,3), 16);
    deepEqual(func(5,6,7), 28);
    deepEqual(func(5,6,9), 30);
});


QUnit.asyncTest('scheduleDeferred', function() {
    var ctxt = 'CTXT';
    var callback = function() {
        ok(this==ctxt, 'this('+this+') == ctxt('+ctxt+')');
        QUnit.start();
    }
    
    PsUtil.scheduleDeferred(callback, ctxt);
});


QUnit.test('extractErrMsg', function() {
    expect(3);
    
    var MSG = 'X';
    var doTest = function(err) {
        var actual = PsUtil.extractErrMsg(err);
        deepEqual(actual, MSG, 'PsUtil.extractErrMsg('+PsObjects.toString(err)+')='+actual);
    }
    
    doTest(MSG);
    doTest(new Error(MSG));
    try {
        throw new Error(MSG);
    } catch(e) {
        doTest(e);
    }
});



/********************/
QUnit.module('PsRand');

QUnit.test('bool', function() {
    var cnt = {
        'true': 0,
        'false': 0
    }
    for(var i=0; i<1000; i++) {
        var rnd = PsRand.bool();
        ok(rnd===true || rnd===false);
        ++cnt[''+rnd];
    }
    
    ok(cnt['true']>=400 && cnt['false']>=400);
});

QUnit.test('integer', function() {
    
    var testNextInteger = function(min, max, count, dopusk, pass) {
        var cnt = {}, i;
        for(i = min; i<=max; i++) {
            cnt[i] = 0;
        }
        for(i=0; i<count; i++) {
            var rnd = PsRand.integer(pass ? min : undefined, pass ? max : undefined);
            var isInt = PsIs.integer(rnd);
            var isReg = cnt.hasOwnProperty(rnd);
            ok(isInt && isReg, '['+rnd+'] is int ? ' + isInt+', is registered ? ' + isReg);
            ++cnt[rnd];
        }
        for(i = min; i<=max; i++) {
            ok(cnt[i]>=dopusk, 'Cnt of '+i+' ('+cnt[i]+') > '+dopusk);
        }
    }
    
    testNextInteger(1,3,1000,250, true);
    testNextInteger(0,10,1000,50, true);
    
    //Проверим поведение без параметров
    testNextInteger(0,9,1000,60, false);
});


QUnit.test('decimal', function() {
    
    var testNextInteger = function(min, max, accuracy, count, pass, intExpected) {
        var cnt = {}, i, passedMin = pass ? min : undefined, passedMax = pass ? max : undefined;
        for(i = min; i<=max; i++) {
            cnt[i] = 0;
        }
        for(i=0; i<count; i++) {
            var rnd = PsRand.decimal(passedMin, passedMax, accuracy);
            var rndNum = Math.round(rnd);
            var isReg = cnt.hasOwnProperty(rndNum);
            var log = 'PsRand.decimal('+ passedMin+', '+passedMax+', '+accuracy+')='+rnd+'. Regustered for ' + rndNum + ' ? ' + isReg;
            if (intExpected) {
                ok (PsIs.integer(rnd) && isReg, log);
            } else {
                ok (PsIs.decimal(rnd) && isReg, log);
            }
            ++cnt[rndNum];
        }
        var dopusk = (count / (1 + max - min))*0.6;
        for(i = min; i<=max; i++) {
            var curDopusk = i==min || i==max ? dopusk/2 : dopusk;
            ok(cnt[i]>=curDopusk, 'Cnt of '+i+' ('+cnt[i]+') > '+curDopusk);
        }
    }
    
    testNextInteger(1, 3,  undefined, 1000, true, false);
    testNextInteger(0, 10, 5, 1000, true, false);
    testNextInteger(0, 7,  0, 1000, true, true);
    
    //Проверим поведение без параметров
    testNextInteger(0,9,7, 1000, false, false);
    testNextInteger(0,9,-1,1000, false, true);
});

QUnit.test('ch', function() {
    var testChars = function(alphabet, upper, useNums, total) {
        var i, ch, ob = {};
        for (i = 0; i<=total; i++) {
            //Без цифр и в нижнем регистре
            ch = PsRand.ch(upper, useNums);
            ok(alphabet.contains(ch), ch);
            if(!ob.hasOwnProperty(ch)) ob[ch] = 0;
            ++ob[ch];
        }
        
        var dopusk = Math.floor(total/alphabet.length * 0.3);
        for(i=0; i<alphabet.length; i++) {
            ch = alphabet[i];
            ok(ob.hasOwnProperty(ch) && ob[ch]>=dopusk, "'"+ch + "' regular("+PsObjects.getValue(ob, ch, 0)+") > "+dopusk);
        }
    }
    
    var total = 2000;
    
    testChars(PsCore.EN_ALPHABET, false, false, total);
    testChars(PsCore.EN_ALPHABET_NUM, false, true, total);
    testChars(PsCore.EN_ALPHABET.toUpperCase(), true, false, total);
    testChars(PsCore.EN_ALPHABET_NUM.toUpperCase(), true, true, total);
    testChars(PsCore.EN_ALPHABET+PsCore.EN_ALPHABET.toUpperCase(), null, false, total);
    testChars(PsCore.EN_ALPHABET_NUM+PsCore.EN_ALPHABET_NUM.toUpperCase(), null, true, total);
});


QUnit.test('string', function() {
    deepEqual(PsRand.string(-1), '');
    deepEqual(PsRand.string(0), '');
    deepEqual(PsRand.string(null).length, 10);
    
    var doTest = function(alphabet, upper, useNums) {
        var randLength = 1000;
        var string = PsRand.string(randLength, upper, useNums);
        ok(string.length==randLength, 'PsRand.string('+randLength+', '+upper+', '+useNums+') = ' + string);
        ok(!PsIs.number(string[0]));
        
        var i, ch, ob = {};
        for(i = 0; i<string.length; i++) {
            //Без цифр и в нижнем регистре
            ch = string[i];
            ok(alphabet.contains(ch), 'Char: ' + ch);
            if(!ob.hasOwnProperty(ch)) ob[ch] = 0;
            ++ob[ch];
        }
        
        var dopusk = Math.floor(randLength/alphabet.length * 0.3);
        for(i=0; i<alphabet.length; i++) {
            ch = alphabet[i];
            ok(ob.hasOwnProperty(ch) && ob[ch]>=dopusk, "'"+ch + "' regular("+PsObjects.getValue(ob, ch, 0)+") > "+dopusk);
        }
    }
    
    doTest(PsCore.EN_ALPHABET, false, false);
    doTest(PsCore.EN_ALPHABET_NUM, false, true);
    doTest(PsCore.EN_ALPHABET.toUpperCase(), true, false);
    doTest(PsCore.EN_ALPHABET_NUM.toUpperCase(), true, true);
    doTest(PsCore.EN_ALPHABET+PsCore.EN_ALPHABET.toUpperCase(), null, false);
    doTest(PsCore.EN_ALPHABET_NUM+PsCore.EN_ALPHABET_NUM.toUpperCase(), null, true);
    doTest(PsCore.EN_ALPHABET+PsCore.EN_ALPHABET.toUpperCase(), null, undefined);
    doTest(PsCore.EN_ALPHABET+PsCore.EN_ALPHABET.toUpperCase(), undefined, undefined);
});



QUnit.test('pseudoId', function() {
    //Базовая генерация pseudoId
    var pseudoId = PsRand.pseudoId();
    ok (pseudoId.length==(1+1+10), pseudoId)
    ok (pseudoId.startsWith('x-'))
    
    //pseudoId не должны повторяться
    var ob = {}
    
    for(var i=1; i<1000;i++) {
        var pseudo = PsRand.pseudoId();
        ok(!ob.hasOwnProperty(pseudo), pseudo);
        ob[pseudo] = 1;
    }
    
    //Префиксы
    var testPseudoId = function(prefix) {
        var pseudoId = PsRand.pseudoId(prefix);
        ok (pseudoId.length==(prefix.length+1+10), pseudoId)
        ok (pseudoId.startsWith(prefix+'-'))
    }
    
    for(var i=1; i<200;i++) {
        testPseudoId(PsRand.string(i, null, true));
    }

});

/**********************/
QUnit.module('Array.prototype');


QUnit.test('clone', function() {
    var a = [1,2,3];
    var b = a;
    
    a.push('a');
    b.push('b', 'c');
    
    deepEqual(a, [1,2,3,'a','b','c']);
    deepEqual(b, [1,2,3,'a','b','c']);
    
    a = [1,2,3];
    b = a.clone();
    
    a.push('a');
    b.push('b', 'c');
    
    deepEqual(a, [1,2,3,'a']);
    deepEqual(b, [1,2,3,'b','c']);
});


QUnit.test('walk', function() {
    expect(5*3 + 2*3 + 1 + 2*3 + 1 + 2*3 + 2 + 4*5 + 1);
    
    var arr = [0,1,2];
    var ctxt = 'CTXT';
    var _idx = 0;
    arr.walk(function(num, idx, prev, next) {
        ok(this==ctxt);
        ok(idx==num);
        ok(_idx++==num);
        if (num===0) {
            ok(prev===null);
            ok(next===1);
        }
        if (num===1) {
            ok(prev===0);
            ok(next===2);
        }
        if (num===2) {
            ok(prev===1);
            ok(next===null);
        }
    }, true, ctxt);
    
    arr.walk(function(num, idx, prev, next) {
        ok(this==ctxt);
        ok(idx==num);
        return ++num;
    }, true, ctxt);
    
    deepEqual(arr, [1,2,3]);
    
    arr.walk(function(num, idx, prev, next) {
        ok(this==ctxt);
        ok(idx==(num-1));
        return ++num;
    }, true, ctxt);
    
    deepEqual(arr, [2,3,4]);
    
    arr.walk(function(num, idx, prev, next) {
        ok(this==ctxt);
        ok(idx==(num-2));
        return --num;
    }, true, ctxt);
    
    deepEqual(arr, [1,2,3]);
    
    arr.push([4,5]);
    
    deepEqual(arr, [1,2,3,[4,5]]);
    
    arr.walk(function(num, idx, prev, next) {
        ok(this==ctxt);
        if (num===1) {
            ok(idx==(num-1));
            ok(prev===null);
            ok(next===2);
        }
        if (num===2) {
            ok(idx==(num-1));
            ok(prev===1);
            ok(next===3);
        }
        if (num===3) {
            ok(idx==(num-1));
            ok(prev===2);
            deepEqual(next, [4,5]);
        }
        if (num===4) {
            ok(idx==0);
            ok(prev===null);
            ok(next===5);
        }
        if (num===5) {
            ok(idx==1);
            ok(prev===4);
            ok(next===null);
        }
    }, true, ctxt);
    
    arr.walk(function(item, idx, prev, next) {
        if(PsIs.array(item)) {
            return 4;
        }
    });
    
    deepEqual(arr, [1,2,3,4]);
});



QUnit.test('walkBack', function() {
    expect(5*3 + 2*3 + 1 + 2*3 + 1 + 2*3 + 2 + 4*5 + 1);
    
    var arr = [0,1,2];
    var ctxt = 'CTXT';
    var _idx = 2;
    arr.walkBack(function(num, idx, prev, next) {
        ok(this==ctxt);
        ok(idx==num);
        ok(_idx--==num);
        if (num===0) {
            ok(prev===null);
            ok(next===1);
        }
        if (num===1) {
            ok(prev===0);
            ok(next===2);
        }
        if (num===2) {
            ok(prev===1);
            ok(next===null);
        }
    }, true, ctxt);
    
    arr.walkBack(function(num, idx, prev, next) {
        ok(this==ctxt);
        ok(idx==num);
        return ++num;
    }, true, ctxt);
    
    deepEqual(arr, [1,2,3]);
    
    arr.walkBack(function(num, idx, prev, next) {
        ok(this==ctxt);
        ok(idx==(num-1));
        return ++num;
    }, true, ctxt);
    
    deepEqual(arr, [2,3,4]);
    
    arr.walkBack(function(num, idx, prev, next) {
        ok(this==ctxt);
        ok(idx==(num-2));
        return --num;
    }, true, ctxt);
    
    deepEqual(arr, [1,2,3]);
    
    arr.push([4,5]);
    
    deepEqual(arr, [1,2,3,[4,5]]);
    
    arr.walkBack(function(num, idx, prev, next) {
        ok(this==ctxt);
        if (num===1) {
            ok(idx==(num-1));
            ok(prev===null);
            ok(next===2);
        }
        if (num===2) {
            ok(idx==(num-1));
            ok(prev===1);
            ok(next===3);
        }
        if (num===3) {
            ok(idx==(num-1));
            ok(prev===2);
            deepEqual(next, [4,5]);
        }
        if (num===4) {
            ok(idx==0);
            ok(prev===null);
            ok(next===5);
        }
        if (num===5) {
            ok(idx==1);
            ok(prev===4);
            ok(next===null);
        }
    }, true, ctxt);
    
    arr.walkBack(function(item, idx, prev, next) {
        if(PsIs.array(item)) {
            return 4;
        }
    });
    
    deepEqual(arr, [1,2,3,4]);
});


QUnit.test('asString', function() {
    deepEqual([1,2].asString(), '[1, 2]');
    deepEqual([].asString(), '[]');
    deepEqual([1,2,[3,4]].asString(), '[1, 2, [3, 4]]');
    deepEqual([1,2,[3,4],{}].asString(), '[1, 2, [3, 4], {}]');
});


QUnit.test('shuffle', function() {
    var a = [1,2,3,4];
    var b = a.clone().shuffle();
    deepEqual(a, [1,2,3,4]);
    ok(a.length==b.length, a+'->'+b);
});


QUnit.test('removeValue', function() {
    var a = [1,2,3,4];
    a.removeValue(3);
    deepEqual(a, [1,2,4]);
});


QUnit.test('equals', function() {
    ok([].equals([]));
    ok([1].equals([1]));
    ok([1,2].equals([1,2]));
    ok(![1,2].equals([2,1]));
    ok(![1,2].equals([1,2], function(i1,i2) {
        return false;
    }));
    ok([1,2].equals([1,2], function(i1,i2) {
        return i1 == i2;
    }));
    ok([1,2].equals(['1','2'], function(i1,i2) {
        return i1 == i2;
    }));
    ok(![1,2].equals(['1','2'], function(i1,i2) {
        return i1 === i2;
    }));
});


QUnit.test('indexOf', function() {
    ok([0,1,2].indexOf(0)===0);
    ok([0,1,2].indexOf(1)===1);
    ok([0,1,2].indexOf(2)===2);
    ok([0,1,2].indexOf(3)===-1);
    
    var f0 = function() {};
    var f1 = function() {};
    var f2 = function() {};
    var f3 = function() {};
    
    ok([f0, f1, f2].indexOf(f0)===0);
    ok([f0, f1, f2].indexOf(f1)===1);
    ok([f0, f1, f2].indexOf(f2)===2);
    ok([f0, f1, f2].indexOf(f3)===-1);
    
    var obj0 = {};
    var obj1 = {};
    var obj2 = {};
    var obj3 = {};
    
    ok([obj0, obj1, obj2].indexOf(obj0)===0);
    ok([obj0, obj1, obj2].indexOf(obj1)===1);
    ok([obj0, obj1, obj2].indexOf(obj2)===2);
    ok([obj0, obj1, obj2].indexOf(obj3)===-1);
    
    ok([obj0, obj1, obj2].indexOf(obj3, function(i1,i2) {
        return true;
    })===0);
    
    ok([obj0, obj1, obj2].indexOf(obj3, function(i1,i2) {
        return i1==obj3;
    })===0);
    
    ok([obj0, obj1, obj2].indexOf(obj3, function(i1,i2) {
        return i2==obj2;
    })===2);

});



QUnit.test('contains', function() {
    ok([0,1,2].contains(0));
    ok([0,1,2].contains(1));
    ok([0,1,2].contains(2));
    ok(![0,1,2].contains(3));
    
    var f0 = function() {};
    var f1 = function() {};
    var f2 = function() {};
    var f3 = function() {};
    
    ok([f0, f1, f2].contains(f0));
    ok([f0, f1, f2].contains(f1));
    ok([f0, f1, f2].contains(f2));
    ok(![f0, f1, f2].contains(f3));
    
    var obj0 = {};
    var obj1 = {};
    var obj2 = {};
    var obj3 = {};
    
    ok([obj0, obj1, obj2].contains(obj0));
    ok([obj0, obj1, obj2].contains(obj1));
    ok([obj0, obj1, obj2].contains(obj2));
    ok(![obj0, obj1, obj2].contains(obj3));
    
    ok([obj0, obj1, obj2].contains(obj3, function(i1,i2) {
        return true;
    }));
    
    ok([obj0, obj1, obj2].contains(obj3, function(i1,i2) {
        return i1==obj3;
    }));
    
    ok([obj0, obj1, obj2].contains(obj3, function(i1,i2) {
        return i2==obj2;
    }));
    
    ok(![obj0, obj1, obj2].contains(obj3, function(i1,i2) {
        return false;
    }));

});



QUnit.test('hasAll', function() {
    ok([0,1,2].hasAll(0));
    ok([0,1,2].hasAll(1));
    ok([0,1,2].hasAll(2));
    ok([0,1,2].hasAll([0,1]));
    ok([0,1,2].hasAll([2,0,1]));
    ok(![0,1,2].hasAll([3,0,1]));
    ok([0,1,2].hasAll([3,0,1], function(i1,i2) {
        return i1==i2 || i1==3;
    }));
    ok([0,1,2].hasAll([2,3,0,1], function(i1,i2) {
        return i1==i2 || i1==3;
    }));
    
    var f0 = function() {};
    var f1 = function() {};
    var f2 = function() {};
    var f3 = function() {};
    
    ok([f0, f1, f2].hasAll(f0));
    ok([f0, f1, f2].hasAll([f2, f0, f1]));
    ok(![f0, f1, f2].hasAll([f2, f0, f1, f3]));
    ok(![f0, f1, f2].hasAll(f3));
    
    var obj0 = {};
    var obj1 = {};
    var obj2 = {};
    var obj3 = {};
    
    ok([obj0, obj1, obj2].hasAll(obj0));
    ok([obj0, obj1, obj2].hasAll([obj1, obj0, obj2]));
    ok(![obj0, obj1, obj2].hasAll([obj1, obj0, obj2, obj3]));
    ok([obj0, obj1, obj2].hasAll([obj1, obj0, obj2, obj3], function(i1, i2) {
        return true;
    }));
});


QUnit.test('hasOneOf', function() {
    ok([0,1,2].hasOneOf(0));
    ok([0,1,2].hasOneOf(1));
    ok([0,1,2].hasOneOf(2));
    ok([0,1,2].hasOneOf([0,1]));
    ok([0,1,2].hasOneOf([2,0,1]));
    ok([0,1,2].hasOneOf([3,0,1]));
    ok(![0,1,2].hasOneOf(3));
    ok([0,1,2].hasOneOf(3, function(i1,i2) {
        return i1==i2 || i1==3;
    }));
    ok([0,1,2].hasOneOf([2,3,0,1]));
    
    var f0 = function() {};
    var f1 = function() {};
    var f2 = function() {};
    var f3 = function() {};
    
    ok([f0, f1, f2].hasOneOf(f0));
    ok([f0, f1, f2].hasOneOf([f2, f0, f1]));
    ok([f0, f1, f2].hasOneOf([f2, f0, f1, f3]));
    ok(![f0, f1, f2].hasOneOf(f3));
    
    var obj0 = {};
    var obj1 = {};
    var obj2 = {};
    var obj3 = {};
    
    ok([obj0, obj1, obj2].hasOneOf(obj0));
    ok([obj0, obj1, obj2].hasOneOf([obj1, obj0, obj2]));
    ok([obj0, obj1, obj2].hasOneOf([obj1, obj0, obj2, obj3]));
    ok([obj0, obj1, obj2].hasOneOf([obj1, obj0, obj2, obj3], function(i1, i2) {
        return true;
    }));
    ok(![obj0, obj1, obj2].hasOneOf(obj3));
    ok([obj0, obj1, obj2].hasOneOf(obj3, function(i1, i2) {
        return i1==i2 || i1==obj3;
    }));
    ok(![obj0, obj1, obj2].hasOneOf({}));
});


QUnit.test('shiftN', function() {
    deepEqual([1,2,3].shiftN(2), [3]);
    deepEqual([1,[2],3].shiftN(3), []);
    deepEqual([1,[2],3].shiftN(5), []);
    deepEqual([1,[2],3].shiftN(), [1, [2], 3]);
});

/**********************/
QUnit.module('PsArrays');


QUnit.test('clone', function() {
    var a = [1,2,3];
    var b = PsArrays.clone(null);
    
    deepEqual(b, []);
    
    b = PsArrays.clone(a);
    
    a.push('a');
    b.push('b', 'c');
    
    deepEqual(a, [1,2,3,'a']);
    deepEqual(b, [1,2,3,'b','c']);
});


QUnit.test('equals', function() {
    ok(PsArrays.equals([1,2], [1,2]));
    ok(!PsArrays.equals([1,2], [2,1]));
    ok(PsArrays.equals(null, null));
    ok(!PsArrays.equals([1,2], null));
    ok(PsArrays.equals([1,2], [2,1], function(i1, i2) {
        return true;
    }));
});


QUnit.test('toArray', function() {
    deepEqual(PsArrays.toArray([1,2]), [1,2]);
    deepEqual(PsArrays.toArray(1), [1]);
    deepEqual(PsArrays.toArray('a'), ['a']);
    deepEqual(PsArrays.toArray(null), []);
    deepEqual(PsArrays.toArray(undefined), []);
    function argumentsToArray() {
        return PsArrays.toArray(arguments);
    }
    deepEqual(argumentsToArray(1,2,3), [1,2,3]);
    deepEqual(argumentsToArray(1,-2,3), [1,-2,3]);
    deepEqual(argumentsToArray(null), [null]);
    deepEqual(argumentsToArray(), []);
    deepEqual(argumentsToArray(undefined), [undefined]);
});


QUnit.test('makeFilter', function() {
    var testFilter = function(filter, item, expectedItem, expectedTake) {
        ok(filter===PsArrays.makeFilter(filter, 'x'));//Возвращается та-же самая функция
        ok(filter!==PsArrays.makeFilter(function(){}, 'x'));
        ok(PsIs.func(filter));
        var result = filter(item);
        ok(PsIs.object(result) && PsObjects.keys2array(result).hasAll(['take', 'item']));
        deepEqual(expectedTake, result.take);
        deepEqual(expectedItem, result.item);
    }
    
    //Деволтный фильтр - ничего не преображает и всё включает
    var filter = PsArrays.makeFilter();
    testFilter(filter, null, null, true);
    testFilter(filter, false, false, true);
    testFilter(filter, undefined, undefined, true);
    
    var CTXT = 'CTXT';
    filter = PsArrays.makeFilter(function(item) {
        deepEqual(this, CTXT);
        return PsIs.number(item)
    }, CTXT);
    testFilter(filter, 1, 1, true);
    testFilter(filter, '1', '1', true);
    testFilter(filter, 'a', 'a', false);
    
    CTXT = function() {};
    filter = PsArrays.makeFilter(function(item) {
        deepEqual(this, CTXT);
        return {
            item: 'x'+item,
            take: PsIs.number(item)
        };
    }, CTXT);
    testFilter(filter, 1, 'x1', true);
    testFilter(filter, '1', 'x1', true);
    testFilter(filter, 'a', 'xa', false);
    
    CTXT = {};
    filter = PsArrays.makeFilter(function(item) {
        deepEqual(this, CTXT);
        var take = PsIs.string(item);
        return {
            item: take ? '1'+item : item,
            take: take
        };
    }, CTXT);
    testFilter(filter, 1, 1, false);
    testFilter(filter, '1', '11', true);
    testFilter(filter, 'a', '1a', true);
    testFilter(filter, '3e+3', '13e+3', true);
    
    CTXT = false;
    filter = PsArrays.makeFilter(function(item) {
        deepEqual(this, CTXT);
        var take = PsIs.number(item);
        return {
            item: take ? 1*item : item,
            take: take
        };
    }, CTXT);
    testFilter(filter, 1, 1, true);
    testFilter(filter, '1', 1, true);
    testFilter(filter, 'a', 'a', false);
    testFilter(filter, '3e+3', 3e+3, true);
});


QUnit.test('filter', function() {
    deepEqual(PsArrays.filter([1,2]), [1,2]);
    deepEqual(PsArrays.filter(null), []);
    deepEqual(PsArrays.filter(undefined), []);
    deepEqual(PsArrays.filter([null, undefined]), [null, undefined]);
    deepEqual(PsArrays.filter([1,2], function(item) {
        return item==1;
    }), [1]);
    deepEqual(PsArrays.filter([0,1,2,3,null], function(item) {
        return item!=0;
    }), [1,2,3,null]);
    deepEqual(PsArrays.filter([NaN,1,2,3], isNaN), [NaN]);
    deepEqual(PsArrays.filter(['0','1',2,3,null, undefined, NaN], PsIs.string), ['0','1']);
    deepEqual(PsArrays.filter(['0','1',2,3,[null, undefined], NaN], function(item) {
        return !PsIs.empty(item);
    }), ['0','1',2,3]);
    //Зайдём в подмассивы, всё вычистим, а после вычистим и сам массив
    deepEqual(PsArrays.filter(['0','1',2,3,[null, undefined, ''], NaN], function(item) {
        return !PsIs.empty(item);
    }), ['0','1',2,3]);
    deepEqual(PsArrays.filter(['0','1',2,3,[null, undefined, '', 'a'], NaN], function(item) {
        return !PsIs.empty(item);
    }), ['0','1',2,3,['a']]);
    //Подмассив вычистим, но самого его оставим
    deepEqual(PsArrays.filter(['0','1',2,3,[null, undefined, ''], NaN], function(item) {
        return !PsIs.empty(item) || PsIs.array(item);
    }), ['0','1',2,3,[]]);
    
    //Сложная функция фильтрации и замены
    var CTXT = 'CTXT';
    var filterImpl = function(item) {
        deepEqual(this, CTXT);
        var isArr = PsIs.array(item);
        var isStr = PsIs.string(item);
        var take = (isArr || isStr) && !PsIs.empty(item);//Берём не пустые строки и массивы
        if(!take) return false;
        if (isArr) {
            //Если массив - положим в конец дополнительный элемент
            item.push('x');
        }
        if (isStr && PsIs.number(item)) {
            //Если строка-число, то преобразуем в число и прибавим 1
            item = item*1+1;
        }
        if (isStr && !PsIs.number(item)) {
            //Если строка-не число, то преобразуем 'x' в начало
            item = 'x'+item;
        }
        return {
            take: true,
            item: item
        }
    }
    var filter = PsArrays.makeFilter(filterImpl, CTXT);
    //Проверим, что фильтр не перебит и контекст остался тем-же
    deepEqual(PsArrays.filter(['0','1',2,3,[null, undefined, ''], NaN], PsArrays.makeFilter(filter, 123)), [1,2]);
    deepEqual(PsArrays.filter(['0','1','2a',[null, undefined, '3'], NaN], PsArrays.makeFilter(filter, 123)), [1,2,'x2a',[4, 'x']]);
    deepEqual(PsArrays.filter(['a','1',['b',[null, undefined, '2']], [3,'4',['',0]]], PsArrays.makeFilter(filter, 123)), ['xa',2,['xb',[3,'x'],'x'], [5,'x']]);

    filter = function(item) {
        return {
            take: PsIs.number(item),
            item: PsIs.number(item) ? 1*item : item
        }
    };
    deepEqual(PsArrays.filter([1,2,null,[3,'a'],['4','3e+5'],['a']], filter), [1,2]);
    
    filter = function(item) {
        return {
            take: PsIs.number(item) || PsIs.array(item),
            item: PsIs.number(item) ? 1*item : item
        }
    };
    deepEqual(PsArrays.filter([1,2,null,[3,'a'],['4','3e+5'],['a']], filter), [1,2,[3],[4,3e+5],[]]);

    filter = function(item) {
        return {
            take: (PsIs.number(item) || PsIs.array(item)) && !PsIs.empty(item),
            item: PsIs.number(item) ? 1*item : item
        }
    };
    deepEqual(PsArrays.filter([1,2,null,[3,'a'],['4','3e+5'],['a'], [0]], filter), [1,2,[3],[4,3e+5]]);
});


QUnit.test('filterEmpty', function() {
    deepEqual(PsArrays.filterEmpty([0,1,2]), [0,1,2]);
    deepEqual(PsArrays.filterEmpty([null,0,1,2]), [0,1,2]);
    deepEqual(PsArrays.filterEmpty([undefined,0,null,1,false,2]), [0,1,2]);
});

QUnit.test('filterNumbers', function() {
    deepEqual(PsArrays.filter([0,1,2], PsIs.number), [0,1,2]);
    deepEqual(PsArrays.filter([null, undefined,0,1,2], PsIs.number), [0,1,2]);
    deepEqual(PsArrays.filter([null, undefined,'0',1,2], PsIs.number), ['0',1,2]);
    deepEqual(PsArrays.filter([null, undefined,'0','a',2,1], PsIs.number), ['0',2,1]);
    deepEqual(PsArrays.filter([null, undefined,'0','a',2,1,0,'-1','1e-2'], PsIs.number), ['0',2,1,0,'-1','1e-2']);
});


QUnit.test('expand', function() {
    deepEqual(PsArrays.expand([1,2,3]), [1,2,3]);
    deepEqual(PsArrays.expand([1,2,3,[4,5]]), [1,2,3,4,5]);
    deepEqual(PsArrays.expand([1,[2],3,[4,5]]), [1,2,3,4,5]);
    deepEqual(PsArrays.expand([1,[2,3],[4,5]]), [1,2,3,4,5]);
    deepEqual(PsArrays.expand([1,[2,3],[4,5,[6]]]), [1,2,3,4,5,6]);
    deepEqual(PsArrays.expand([1,[2,3],null,[undefined],[4,5]]), [1,2,3,null,undefined,4,5]);
    deepEqual(PsArrays.expand([1,[2,3],null,[undefined],[4,5]], PsIs.empty), [null,undefined]);
    deepEqual(PsArrays.expand([1,[2,3],null,[undefined],[4,5]], PsIs.number), [1,2,3,4,5]);
    deepEqual(PsArrays.expand([2,[1,3],null,[undefined],[4,5]], function(item) {
        return {
            take: PsIs.number(item),
            item: PsIs.number(item) ? ++item : item
        }
    }), [3,2,4,5,6]);
    deepEqual(PsArrays.expand(['2',['1',3,'a'],null,[undefined],['4','5','b'], '2e+3'], function(item) {
        return {
            take: PsIs.string(item),
            item: PsUtil.toNumber(item, 5)
        }
    }), [2,1,5,4,5,5,2e+3]);
});


QUnit.test('extractSubArrays', function() {
    deepEqual(PsArrays.extractSubArrays(null), []);
    deepEqual(PsArrays.extractSubArrays([[],[]]), []);
    deepEqual(PsArrays.extractSubArrays(undefined), []);
    deepEqual(PsArrays.extractSubArrays(false), [[false]]);
    deepEqual(PsArrays.extractSubArrays([[1,[2,[3]]]]), [[1],[2],[3]]);
    deepEqual(PsArrays.extractSubArrays([1,2,3]), [[1,2,3]]);
    deepEqual(PsArrays.extractSubArrays([1,2,3,[4]]), [[1,2,3],[4]]);
    deepEqual(PsArrays.extractSubArrays([1,[2,3],null,[undefined],[4,5]]), [[1,null],[2,3],[undefined],[4,5]]);
    deepEqual(PsArrays.extractSubArrays([1,2,[3,4,[5,6,7,[8]]],9]), [[1,2,9],[3,4],[5,6,7],[8]]);
    deepEqual(PsArrays.extractSubArrays([['x', 'a'],2,1]), [['x','a'],[2,1]]);
    deepEqual(PsArrays.extractSubArrays([['x', 'a'],2,[3],1,[4,[5,[6],['x']]]]), [['x','a'],[2,1],[3],[4],[5],[6],['x']]);
    deepEqual(PsArrays.extractSubArrays([[1,2],[3,4],5,6,7,[8],9]), [[1,2],[3,4],[5,6,7,9],[8]]);
    
    //filter
    deepEqual(PsArrays.extractSubArrays([[1,2],[3,4],5,6,7,[8],9], PsIs.number), [[1,2],[3,4],[5,6,7,9],[8]]);
    deepEqual(PsArrays.extractSubArrays([[1,2],[3,4],5,6,7,[8,'x'],9], PsIs.number), [[1,2],[3,4],[5,6,7,9],[8]]);
    deepEqual(PsArrays.extractSubArrays([[1,2],[3,4],5,6,7,[8,'x'],9], PsIs.string), [['x']]);
    deepEqual(PsArrays.extractSubArrays([[1,2],[3,4],5,6,7,[8],9], PsIs.func), []);
    
    //converter
    var CTXT = 'CTXT';
    var filter = PsArrays.makeFilter(function(item) {
        deepEqual(this, CTXT);
        return PsIs.number(item);
    }, CTXT);
    deepEqual(PsArrays.extractSubArrays([[1,2],[3,4],5,6,7,[8],9,'a'], filter), [[1,2],[3,4],[5,6,7,9],[8]]);
    deepEqual(PsArrays.extractSubArrays([[1,2,'a'],[3,'y',4],5,6,7,[8],9,'a'], filter), [[1,2],[3,4],[5,6,7,9],[8]]);
    
    filter = PsArrays.makeFilter(PsArrays.makeFilter(function(item) {
        deepEqual(this, CTXT);
        return {
            take: PsIs.string(item),
            item: 'x'+item
        }
    }, CTXT), 123);
    deepEqual(PsArrays.extractSubArrays([[1,2,'a'],[3,'y',4],5,6,7,[8],9,'a'], filter), [['xa'],['xy'],['xa']]);
    
    deepEqual(PsArrays.extractSubArrays([[1,2],[3,4],5,6,7,[8,'x'],9], PsIs.number), [[1,2],[3,4],[5,6,7,9],[8]]);
    deepEqual(PsArrays.extractSubArrays([[1,2],[3,4],5,6,7,[8,'x'],9], PsIs.string), [['x']]);
    deepEqual(PsArrays.extractSubArrays([[1,2],[3,4],5,6,7,[8],9], PsIs.func), []);
    
    filter = function(item) {
        return {
            take: PsIs.number(item),
            item: PsIs.number(item) ? 1*item : item
        }
    };
    deepEqual(PsArrays.extractSubArrays([1,2,null,[3,'a'],['4','3e+5'],['a']], filter), [[1,2],[3],[4, 3e+5]]);
    

});


QUnit.test('unique', function() {
    deepEqual([1,2,3], PsArrays.unique([1,2,3]));
    deepEqual([1,2,3,4], PsArrays.unique([1,2,3,4]));
    deepEqual([1,2,3], PsArrays.unique([1,2,3,3]));
    deepEqual([1,2,3], PsArrays.unique([1,2,3,null,NaN,false]));
    deepEqual([0,1,2,3], PsArrays.unique([0,1,2,3,null,NaN,false]));
    deepEqual([0,[1,2],3,[4,4]], PsArrays.unique([0,[1,2],3,[4,4]]));
});


QUnit.test('combination', function() {
    deepEqual(PsArrays.filterEmpty(PsArrays.expand([0,1,[2,3],null,[undefined],[4,5]])), [0,1,2,3,4,5]);
    deepEqual(PsArrays.unique(PsArrays.filterEmpty(PsArrays.expand([0,1,[2,3],null,[undefined],[4,5],[5]]))), [0,1,2,3,4,5]);
    //unique тоже удаляет пустые элементы, кроме ноля
    deepEqual(PsArrays.unique(PsArrays.expand([0,1,[2,3],null,[undefined],[4,5],[5]])), [0,1,2,3,4,5]);
    var arr = [undefined, null, NaN, false, 3, 2, 0, 1];
    deepEqual(PsArrays.unique(arr), PsArrays.filterEmpty(arr));

});

QUnit.test('getDiff', function() {
    var ob1 = PsArrays.getDiff([0,1], [1,2]);
    deepEqual(ob1.comm, [1]);
    deepEqual(ob1.diff, [0,2]);
    deepEqual(ob1.a1has, [0]);
    deepEqual(ob1.a2has, [2]);
    
    
    var ob2 = PsArrays.getDiff([0,1,2,null,NaN], [1,2,undefined,false,[]]);
    deepEqual(ob2.comm, [1,2]);
    deepEqual(ob2.diff, [0]);
    deepEqual(ob2.a1has, [0]);
    deepEqual(ob2.a2has, []);
    
    
    var ob3 = PsArrays.getDiff([2,0,1], [3,1,2]);
    deepEqual(ob3.comm, [1,2]);
    deepEqual(ob3.diff, [0,3]);
    deepEqual(ob3.a1has, [0]);
    deepEqual(ob3.a2has, [3]);
});


QUnit.test('array2arrays', function() {
    deepEqual(PsArrays.array2arrays([0,1,2,3], 1), [[0], [1], [2], [3]]);
    deepEqual(PsArrays.array2arrays([0,1,2,3], 2), [[0,1], [2,3]]);
    deepEqual(PsArrays.array2arrays([0,1,2,3], 3), [[0,1,2], [3]]);
    deepEqual(PsArrays.array2arrays([0,1,2,3], 4), [[0,1,2,3]]);
    deepEqual(PsArrays.array2arrays([null], 4), [[null]]);
    deepEqual(PsArrays.array2arrays(null, 4), []);
    deepEqual(PsArrays.array2arrays([0,1,2,[3,4,[5,6,[7]]]], 3), [[0,1,2],[3,4,5],[6,7]]);
    deepEqual(PsArrays.array2arrays([0,1,2,[3,4,[5,6,[7]]]], 3), [[0,1,2],[3,4,5],[6,7]]);
});



QUnit.test('string2arrays', function() {
    deepEqual(PsArrays.string2arrays('1234', 2), [['1','2'],['3','4']]);
    deepEqual(PsArrays.string2arrays('1234', 3), [['1','2','3'],['4']]);
    deepEqual(PsArrays.string2arrays('1234', 4), [['1','2','3','4']]);
    deepEqual(PsArrays.string2arrays('1234a', 1), [['1'],['2'],['3'],['4'],['a']]);
});


QUnit.test('joinExpanded', function() {
    deepEqual(PsArrays.joinExpanded([1,2]), '12');
    deepEqual(PsArrays.joinExpanded([0,1,2]), '012');
    deepEqual(PsArrays.joinExpanded([0,1,['a','b',[false]],2]), '01abfalse2');
    deepEqual(PsArrays.joinExpanded([[0],1,['a','b',[false]],2]), '01abfalse2');
});


QUnit.test('inArray', function() {
    ok(PsArrays.inArray(1, [1,2]));
    ok(PsArrays.inArray(1, 1));
    ok(PsArrays.inArray(1, ['1',2]));
    ok(PsArrays.inArray(false, ['1',false]));
    ok(PsArrays.inArray(undefined, ['1',undefined]));
    ok(PsArrays.inArray('+', ['+', '-', '*', ':']));
    ok(PsArrays.inArray('-', ['+', '-', '*', ':']));
    ok(PsArrays.inArray('*', ['+', '-', '*', ':']));
    ok(PsArrays.inArray(':', ['+', '-', '*', ':']));
    ok(!PsArrays.inArray('/', ['+', '-', '*', ':']));
    ok(!PsArrays.inArray('!', ['+', '-', '*', ':']));
    ok(!PsArrays.inArray('A', ['+', '-', '*', ':']));
    
    var options ={
        ctxt: 1,
        parent: 2,
        item: 3,
        data: 4
    }
    for (var v in options) {
        ok(PsArrays.inArray(v, ['ctxt', 'parent', 'item', 'data']));
    }
    for (var v in options) {
        ok(!PsArrays.inArray(v, ['_ctxt', '_parent', '_item', '_data']));
    }
});


QUnit.test('firstItem', function() {
    deepEqual(PsArrays.firstItem([1]), 1);
    deepEqual(PsArrays.firstItem([1,2]), 1);
    deepEqual(PsArrays.firstItem([null,1,2]), null);
    deepEqual(PsArrays.firstItem(['x',1,2]), 'x');
    deepEqual(PsArrays.firstItem([[1,2],'x',1,2]), [1,2]);
    deepEqual(PsArrays.firstItem([]), undefined);
    deepEqual(PsArrays.firstItem(null), undefined);
    deepEqual(PsArrays.firstItem(), undefined);
});


QUnit.test('lastItem', function() {
    deepEqual(PsArrays.lastItem([1]), 1);
    deepEqual(PsArrays.lastItem([1,2]), 2);
    deepEqual(PsArrays.lastItem([1,2,null]), null);
    deepEqual(PsArrays.lastItem([1,2,'x']), 'x');
    deepEqual(PsArrays.lastItem(['x',1,2,[1,2]]), [1,2]);
    deepEqual(PsArrays.lastItem([]), undefined);
    deepEqual(PsArrays.lastItem(null), undefined);
    deepEqual(PsArrays.lastItem(), undefined);
});


QUnit.test('nextItem', function() {
    deepEqual(PsArrays.nextItem([1,2],1), 2);
    deepEqual(PsArrays.nextItem([1,2],2), null);
    deepEqual(PsArrays.nextItem([1,2,null,3],null), 3);
    deepEqual(PsArrays.nextItem([1,false,null,3],false), null);
    deepEqual(PsArrays.nextItem([1,false,null,3],false), null);
    deepEqual(PsArrays.nextItem([1,false,null,3],1), false);
    deepEqual(PsArrays.nextItem([1,false,null,3], null, function(i1,i2) {
        return i1===i2;
    }), 3);
});


QUnit.test('nextOrFirstItem', function() {
    deepEqual(PsArrays.nextOrFirstItem([1,2],1), 2);
    deepEqual(PsArrays.nextOrFirstItem([1,2],2), 1);
    deepEqual(PsArrays.nextOrFirstItem([1,false,null],false), null);
    deepEqual(PsArrays.nextOrFirstItem([1,false,null],null), 1);
});


QUnit.test('centralItem', function() {
    deepEqual(PsArrays.centralItem([1,2,3]),2);
    deepEqual(PsArrays.centralItem([1,2,3,4]),2);
    deepEqual(PsArrays.centralItem([1,2,3,4,5]),3);
    deepEqual(PsArrays.centralItem([1,2,3,4,5]),3);
    deepEqual(PsArrays.centralItem([1]),1);
    deepEqual(PsArrays.centralItem([]),null);
});


QUnit.test('centralItem', function() {
    deepEqual(PsArrays.centralItem([1,2,3]),2);
    deepEqual(PsArrays.centralItem([1,2,3,4]),2);
    deepEqual(PsArrays.centralItem([1,2,3,4,5]),3);
    deepEqual(PsArrays.centralItem([1,2,3,4,5]),3);
    deepEqual(PsArrays.centralItem([1]),1);
    deepEqual(PsArrays.centralItem([]),null);
});


/**********************/
QUnit.module('PsObjects');


QUnit.test('keys2array', function() {
    var ob = {
        a: 1,
        c: 2,
        b: 3
    }
    
    deepEqual(PsObjects.keys2array(ob), ['a', 'b', 'c']);
    
    var Class = function() {
        this.a = 1;
        this.c = 2;
        this.b = 3;
    }
    
    Class.prototype.d = 4;
    
    var obj = new Class();
    
    deepEqual(PsObjects.keys2array(obj), ['a', 'b', 'c']);
    ok(!obj.hasOwnProperty('d'));
    ok(obj.d===4);
});


QUnit.test('hasKeys', function() {
    var ob = {
        a: 1,
        c: 2,
        b: 3
    }
    
    ok(PsObjects.hasKeys(ob));
    
    var Class1 = function() {
        this.a = 1;
        this.c = 2;
        this.b = 3;
    }
    Class1.prototype.d = 4;
    var obj1 = new Class1();
    ok(PsObjects.hasKeys(obj1));
    ok(!obj1.hasOwnProperty('d'));
    ok(obj1.d===4);
    
    var Class2 = function() {
    }
    Class2.prototype.d = 4;
    var obj2 = new Class2();
    ok(!PsObjects.hasKeys(obj2));
    ok(!obj2.hasOwnProperty('d'));
    ok(obj2.d===4);
});


QUnit.test('getValue', function() {
    var Class = function() {
        this.a = 1;
        this.b = 2;
        this.c = 3;
    }
    
    Class.prototype.d = 4;
    
    var obj = new Class();
    
    deepEqual(PsObjects.getValue(obj, 'a'), 1);
    deepEqual(PsObjects.getValue(obj, 'b'), 2);
    deepEqual(PsObjects.getValue(obj, 'c'), 3);
    deepEqual(PsObjects.getValue(obj, 'd'), null);
    deepEqual(PsObjects.getValue(obj, 'e'), null);
    deepEqual(PsObjects.getValue(obj, 'e', 10), 10);
    
    obj.e = 5;
    
    deepEqual(PsObjects.getValue(obj, 'e', 10), 5);
});


QUnit.test('hasValue', function() {
    var Class = function() {
        this.a = 1;
        this.b = '2';
        this.c = 3;
    }
    
    Class.prototype.d = 4;
    
    var obj = new Class();
    
    ok(PsObjects.hasValue(obj, '1'));
    ok(PsObjects.hasValue(obj, '2'));
    ok(PsObjects.hasValue(obj, '3'));
    ok(!PsObjects.hasValue(obj, 4));
    
    obj.e = 5;
    
    ok(PsObjects.hasValue(obj, '5'));
    
    var strictComparator = function(val, item) {
        return val === item;
    }
    
    ok(!PsObjects.hasValue(obj, '1', strictComparator));
    ok(PsObjects.hasValue(obj, '2', strictComparator));
    ok(!PsObjects.hasValue(obj, '3', strictComparator));
    
});


QUnit.test('toString', function() {
    var ob = {
        a: 1,
        c: 2,
        b: 3
    }
    
    deepEqual(PsObjects.toString({}), '{}');
    deepEqual(PsObjects.toString([]), '[]');
    deepEqual(PsObjects.toString(null), 'null');
    deepEqual(PsObjects.toString(false), 'false');
    deepEqual(PsObjects.toString(undefined), 'undefined');
    deepEqual(PsObjects.toString([1,2,[3,4]]), '[1, 2, [3, 4]]');
    deepEqual(PsObjects.toString(ob), '{a: 1, b: 3, c: 2}');
    deepEqual(PsObjects.toString(new Error('xxx')), 'Error: xxx');

    ob.e = [1,2];
    deepEqual(PsObjects.toString(ob), '{a: 1, b: 3, c: 2, e: [1, 2]}');
    
    ob.d = {
        arr: [3, 1]
    };
    deepEqual(PsObjects.toString(ob), '{a: 1, b: 3, c: 2, d: {arr: [3, 1]}, e: [1, 2]}');
    
    ob.f = function () {};
    deepEqual(PsObjects.toString(ob), '{a: 1, b: 3, c: 2, d: {arr: [3, 1]}, e: [1, 2], f: function () {}}');
    
    function argumentsToString() {
        return PsObjects.toString(arguments);
    }
    deepEqual(argumentsToString(1,2,3), '[1, 2, 3]');
    deepEqual(argumentsToString(1,2,[3,'a']), '[1, 2, [3, a]]');
    deepEqual(argumentsToString(1,2,[3,'a'],null,undefined), '[1, 2, [3, a], null, undefined]');
    deepEqual(argumentsToString(1,2,[3,{
        a:1
    }]), '[1, 2, [3, {a: 1}]]');
});


QUnit.test('toStringData', function() {
    var ob = {
        a: 1,
        c: 2,
        b: 3,
        f: function () {
        
        }
    }
    
    deepEqual(PsObjects.toStringData({}), '{}');
    deepEqual(PsObjects.toStringData([]), '[]');
    deepEqual(PsObjects.toStringData(null), 'null');
    deepEqual(PsObjects.toStringData(false), 'false');
    deepEqual(PsObjects.toStringData(undefined), 'undefined');
    deepEqual(PsObjects.toStringData([1,2,[3,4]]), '[1, 2, [3, 4]]');
    deepEqual(PsObjects.toStringData(ob), '{a: 1, b: 3, c: 2}');
    
    ob.e = [1,2];
    deepEqual(PsObjects.toStringData(ob), '{a: 1, b: 3, c: 2, e: [1, 2]}');
    
    ob.d = {
        arr: [3, 1]
    };
    deepEqual(PsObjects.toStringData(ob), '{a: 1, b: 3, c: 2, d: {arr: [3, 1]}, e: [1, 2]}');
    
    ob.f = function () {};
    deepEqual(PsObjects.toStringData(ob), '{a: 1, b: 3, c: 2, d: {arr: [3, 1]}, e: [1, 2]}');
    
    ob = {
        a: 1,
        c: 3,
        b: 2,
        f: function () {
        
        },
        o: {
            a: 1,
            f: function () {
            
            }
        }
    }
    deepEqual(PsObjects.toStringData(ob), '{a: 1, b: 2, c: 3, o: {a: 1}}');
    deepEqual(PsObjects.toStringData(ob, ['a']), '{b: 2, c: 3, o: {a: 1}}');
});



QUnit.test('values2array', function() {
    var Class = function() {
        this.a = 1;
        this.c = 2;
        this.b = 3;
    }
    
    Class.prototype.d = 4;
    
    var obj = new Class();
    
    deepEqual(PsObjects.values2array(obj), [1,3,2]);
    deepEqual(PsObjects.values2array(obj, function(i1, i2) {
        return i2-i1;
    }), [3,2,1]);
    deepEqual(PsObjects.values2array(obj, function(i1, i2) {
        return i1-i2;
    }), [1,2,3]);
});



QUnit.test('clone', function() {
    var original = {
        a: 1,
        b: 2,
        c: 3,
        f: function () {
        
        }
    };
    var cloned = PsObjects.clone(original);
    ok(PsObjects.toString(original) === PsObjects.toString(cloned));
    
    original.a = 4;
    deepEqual(original.a, 4);
    deepEqual(cloned.a, 1);
    
    //Уберём функции при клонировании
    cloned = PsObjects.clone(original, [], true);
    delete original['f'];
    ok(PsObjects.toString(original) === PsObjects.toString(cloned));
    
    
    //Уберём некоторые ключи
    cloned = PsObjects.clone(original, 'a', true);
    delete original['a'];
    ok(PsObjects.toString(original) === PsObjects.toString(cloned));
    
    //Уберём некоторые ключи
    cloned = PsObjects.clone(original, ['b', 'c'], true, 'b');
    delete original['c'];
    ok(PsObjects.toString(original) === PsObjects.toString(cloned));
    
    //Ссылки объектов на себя
    original = {
        a: 1,
        b: 2,
        c: 3,
        f: function () {
        },
        o: {
            a: 1,
            b: 2,
            f: function () {
            }
        }
    }
    cloned = PsObjects.clone(original);
    cloned.o.a = 5;
    deepEqual(original.o.a, 1);
    deepEqual(cloned.o.a, 5);
    
    //Удалим функции и элементы первого уровня
    cloned = PsObjects.clone(original, 'a', true);
    delete original['a'];
    delete original['f'];
    delete original.o['f'];
    ok(PsObjects.toString(original) === PsObjects.toString(cloned));
    
    //Сохранение функций при передаче forceInclude
    original = {
        a: 1,
        b: 2,
        f: function () {
        },
        o: {
            a: 1,
            b: 2,
            f: function () {
            }
        }
    }
    cloned = PsObjects.clone(original, ['b'], true, 'f');
    delete original['b'];
    ok(PsObjects.toString(original) === PsObjects.toString(cloned));
    
    cloned = PsObjects.clone(original, ['a'], true, 'f');
    delete original['a'];
    ok(PsObjects.toString(original) === PsObjects.toString(cloned));
    
    cloned = PsObjects.clone(original, ['a'], true);
    delete original['f'];
    delete original.o['f'];
    ok(PsObjects.toString(original) === PsObjects.toString(cloned));
});




/**********************/
QUnit.module('String.prototype');


QUnit.test('contains', function() {
    ok('abc'.contains('a'));
    ok('abc'.contains('b'));
    ok('abc'.contains('c'));
    ok('abc'.contains('ab'));
    ok('abc'.contains('bc'));
    ok('abc'.contains('abc'));
    ok(!'abc'.contains('abcd'));
    ok(!'abc'.contains(null));
});


QUnit.test('startsWith', function() {
    ok('abc'.startsWith('a'));
    ok('abc'.startsWith('ab'));
    ok('abc'.startsWith('abc'));
    ok(!'abc'.startsWith('abcd'));
    ok(!'abc'.startsWith(null));
    ok(!'abc'.startsWith('а'));//Русская 'а'
});

QUnit.test('ensureStartsWith', function() {
    deepEqual('abc'.ensureStartsWith('a'), 'abc');
    deepEqual('abc'.ensureStartsWith('ab'), 'abc');
    deepEqual('abc'.ensureStartsWith('abc'), 'abc');
    deepEqual('abc'.ensureStartsWith('abcd'), 'abcdabc');
    deepEqual('abc'.ensureStartsWith('1'), '1abc');
    deepEqual('abc'.ensureStartsWith(null), 'nullabc', 'abc'.ensureStartsWith(null));
    deepEqual('абв'.ensureStartsWith('г'), 'габв');
});

QUnit.test('endsWith', function() {
    ok('abc'.endsWith('c'));
    ok('abc'.endsWith('bc'));
    ok('abc'.endsWith('abc'));
    ok(!'abc'.endsWith('abcd'));
    ok(!'abc'.endsWith(null));
    ok(!'abc'.endsWith('с'));//Русская 'с'
});

QUnit.test('ensureEndsWith', function() {
    deepEqual('abc'.ensureEndsWith('c'), 'abc');
    deepEqual('abc'.ensureEndsWith('bc'), 'abc');
    deepEqual('abc'.ensureEndsWith('abc'), 'abc');
    deepEqual('abc'.ensureEndsWith('abcd'), 'abcabcd');
    deepEqual('abc'.ensureEndsWith('1'), 'abc1');
    deepEqual('abc'.ensureEndsWith(null), 'abcnull', 'abc'.ensureEndsWith(null));
    deepEqual('абв'.ensureEndsWith('г'), 'абвг');
});

QUnit.test('htmlEntities', function() {
    var notSave = 'now <html> \n & is save';
    var save = 'now &lt;html&gt; <br/> &amp; is save'
    deepEqual(notSave.htmlEntities(), save, save);
});

QUnit.test('firstCharToUpper', function() {
    deepEqual('abc'.firstCharToUpper(), 'Abc');
    deepEqual('абв'.firstCharToUpper(), 'Абв');
    deepEqual(''.firstCharToUpper(), '');
});

QUnit.test('shuffle', function() {
    var string = 'abcdef';
    var shuffled = string.shuffle();
    var notOnPlace = 0;
    ok(string.length===shuffled.length);
    for(var i=0; i<string.length; i++) {
        var ch1 = string[i];
        var ch2 = shuffled[i];
        ok(shuffled.contains(ch1));
        notOnPlace += ch1==ch2 ? 0 : 1;
    }
    ok(notOnPlace>0, string+'->'+shuffled);
});

QUnit.test('charsCnt', function() {
    deepEqual('abcabd'.charsCnt('a'), 2);
    deepEqual('abcabd'.charsCnt('b'), 2);
    deepEqual('abcabd'.charsCnt('c'), 1);
    deepEqual('abcabd'.charsCnt('d'), 1);
    deepEqual('abcabd'.charsCnt('e'), 0);
    
    deepEqual('абвга'.charsCnt('а'), 2);
    deepEqual('абвга'.charsCnt('б'), 1);
    deepEqual('абвга'.charsCnt('е'), 0);
    deepEqual('абвга'.charsCnt('f'), 0);
});


QUnit.test('getFirstChar', function() {
    deepEqual('abcd'.getFirstChar(null), 'a');
    deepEqual('abcd'.getFirstChar(false), 'a');
    deepEqual('abcd'.getFirstChar(-1), '');
    deepEqual('abcd'.getFirstChar(), 'a');
    deepEqual('abcd'.getFirstChar(1), 'a');
    deepEqual('abcd'.getFirstChar(2), 'ab');
    deepEqual('abcd'.getFirstChar(3), 'abc');
    deepEqual('abcd'.getFirstChar(4), 'abcd');
    deepEqual('abcd'.getFirstChar(5), 'abcd');
});

QUnit.test('removeFirstChar', function() {
    deepEqual('abcd'.removeFirstChar(null), 'bcd');
    deepEqual('abcd'.removeFirstChar(false), 'bcd');
    deepEqual('abcd'.removeFirstChar(-1), 'abcd');
    deepEqual('abcd'.removeFirstChar(0), 'abcd');
    deepEqual('abcd'.removeFirstChar(), 'bcd');
    deepEqual('abcd'.removeFirstChar(1), 'bcd');
    deepEqual('abcd'.removeFirstChar(2), 'cd');
    deepEqual('abcd'.removeFirstChar(3), 'd');
    deepEqual('abcd'.removeFirstChar(4), '');
    deepEqual('abcd'.removeFirstChar(5), '');
    deepEqual('abcd'.removeFirstChar().removeFirstChar(), 'cd');
});

QUnit.test('removeFirstCharIf', function() {
    deepEqual('Abcd'.removeFirstCharIf('a'), 'Abcd');
    deepEqual('abcd'.removeFirstCharIf('A'), 'abcd');
    deepEqual('abcd'.removeFirstCharIf('a'), 'bcd');
    deepEqual('abcd'.removeFirstCharIf('b'), 'abcd');
    deepEqual('абвг'.removeFirstCharIf('а'), 'бвг');
    deepEqual('абвг'.removeFirstCharIf('б'), 'абвг');
});


QUnit.test('removeFirstCharWhile', function() {
    deepEqual('Abcd'.removeFirstCharWhile('a'), 'Abcd');
    deepEqual('abcd'.removeFirstCharWhile('A'), 'abcd');
    deepEqual('abcd'.removeFirstCharWhile('a'), 'bcd');
    deepEqual('aabcd'.removeFirstCharWhile('a'), 'bcd');
    deepEqual('abcd'.removeFirstCharWhile('b'), 'abcd');
    deepEqual('абвг'.removeFirstCharWhile('а'), 'бвг');
    deepEqual('абвг'.removeFirstCharWhile('б'), 'абвг');
    deepEqual('000000'.removeFirstCharWhile('0'), '');
    deepEqual('1000000'.removeFirstCharWhile('0'), '1000000');
    deepEqual('000000123'.removeFirstCharWhile('0'), '123');
    deepEqual('00100'.removeFirstCharWhile('0'), '100');
});


QUnit.test('getLastChar', function() {
    deepEqual('abcd'.getLastChar(null), 'd');
    deepEqual('abcd'.getLastChar(false), 'd');
    deepEqual('abcd'.getLastChar(-1), '');
    deepEqual('abcd'.getLastChar(0), '');
    deepEqual('abcd'.getLastChar(), 'd');
    deepEqual('abcd'.getLastChar(1), 'd');
    deepEqual('abcd'.getLastChar(2), 'cd');
    deepEqual('abcd'.getLastChar(3), 'bcd');
    deepEqual('abcd'.getLastChar(4), 'abcd');
    deepEqual('abcd'.getLastChar(5), 'abcd');
});

QUnit.test('removeLastChar', function() {
    deepEqual('abcd'.removeLastChar(null), 'abc');
    deepEqual('abcd'.removeLastChar(false), 'abc');
    deepEqual('abcd'.removeLastChar(-1), 'abcd');
    deepEqual('abcd'.removeLastChar(0), 'abcd');
    deepEqual('abcd'.removeLastChar(), 'abc');
    deepEqual('abcd'.removeLastChar(1), 'abc');
    deepEqual('abcd'.removeLastChar(2), 'ab');
    deepEqual('abcd'.removeLastChar(3), 'a');
    deepEqual('abcd'.removeLastChar(4), '');
    deepEqual('abcd'.removeLastChar(5), '');
    deepEqual('abcd'.removeLastChar().removeLastChar(), 'ab');
});


QUnit.test('removeLastCharIf', function() {
    deepEqual('abcD'.removeLastCharIf('d'), 'abcD');
    deepEqual('abcd'.removeLastCharIf('D'), 'abcd');
    deepEqual('abcd'.removeLastCharIf('d'), 'abc');
    deepEqual('abcd'.removeLastCharIf('c'), 'abcd');
    deepEqual('абвг'.removeLastCharIf('г'), 'абв');
    deepEqual('абвг'.removeLastCharIf('в'), 'абвг');
});


QUnit.test('removeLastCharWhile', function() {
    deepEqual('abcD'.removeLastCharWhile('d'), 'abcD');
    deepEqual('abcd'.removeLastCharWhile('D'), 'abcd');
    deepEqual('abcd'.removeLastCharWhile('d'), 'abc');
    deepEqual('abcdd'.removeLastCharWhile('d'), 'abc');
    deepEqual('abcd'.removeLastCharWhile('c'), 'abcd');
    deepEqual('абвг'.removeLastCharWhile('г'), 'абв');
    deepEqual('абвг'.removeLastCharWhile('в'), 'абвг');
    deepEqual('000000'.removeLastCharWhile('0'), '');
    deepEqual('00100'.removeLastCharWhile('0'), '001');
    deepEqual('0000001'.removeLastCharWhile('0'), '0000001');
    deepEqual('123000000'.removeLastCharWhile('0'), '123');
});


QUnit.test('replaceAll', function() {
    deepEqual('axbxc'.replaceAll('x', 1), 'a1b1c');
    deepEqual('axbxc'.replaceAll('a', 1), '1xbxc');
    deepEqual('аубув'.replaceAll('у', 'т'), 'атбтв');
    deepEqual('aXbXc'.replaceAll('x', 1), 'aXbXc');
});


QUnit.test('hashCode', function() {
    [
    ['', 0],
    ['0', 48],
    ['a', 97],
    ['A', 65],
    ['a+b*c d 12DS!~$#!@ ', -486195798]
    ].walk(function(testCase) {
        var string = testCase[0];
        var hash = testCase[1];
        deepEqual(string.hashCode(), hash, "'"+string+"'.hasCode()="+hash);
    });
    
    var given = [];
    for (var i=0; i<1000; i++) {
        var string = PsRand.string(10, null, true);
        var hash = string.hashCode();
        ok(PsIs.integer(hash) && hash!=0 && !given.contains(hash), "'"+string+"'.hasCode()="+hash);
        given.push(hash);
    }
});


QUnit.test('splitSave', function() {
    deepEqual('axbxc'.split('x', 1), ['a']);
    deepEqual('axbxc'.split('x', 2), ['a', 'b']);
    deepEqual('axbxc'.split('x', 3), ['a', 'b', 'c']);
    deepEqual('axbxc'.split('x', 4), ['a', 'b', 'c']);
    deepEqual('axbxc'.splitSave('x', 1), ['axbxc']);
    deepEqual('axbxc'.splitSave('x', 2), ['a', 'bxc']);
    deepEqual('axbxc'.splitSave('x', 3), ['a', 'b', 'c']);
    deepEqual('axbxc'.splitSave('x', 4), ['a', 'b', 'c']);
    deepEqual('аубув'.splitSave('у', 1), ['аубув']);
    deepEqual('аубув'.splitSave('у', 2), ['а', 'був']);
    deepEqual('аубув'.splitSave('у', 3), ['а', 'б', 'в']);
    deepEqual('аубув'.splitSave('у', 4), ['а', 'б', 'в']);
    
    deepEqual('a|b|c'.split('|',1), ['a']);
    deepEqual('a|b|c'.split('|',2), ['a', 'b']);
    deepEqual('a|b|c'.splitSave('|',1), ['a|b|c']);
    deepEqual('a|b|c'.splitSave('|',2), ['a', 'b|c']);
});



/**********************/
QUnit.module('PsStrings');

QUnit.test('trim', function() {
    var tests = [['a', 'a'], [null, ''], [false, 'false'], [1, '1'], [' a', 'a'], ['a ', 'a'], [' a ', 'a']];
    for (var i=0; i<tests.length; i++) {
        var notTrimmed = tests[i][0];
        var trimmed = tests[i][1];
        ok(PsStrings.trim(notTrimmed) === trimmed, 'PsStrings.trim("'+notTrimmed+'")="'+trimmed+'"');
    }
});

QUnit.test('replaceOneByOne', function() {
    deepEqual(PsStrings.replaceOneByOne('a{}b{}c', '{}', [1,2]), 'a1b2c');
    deepEqual(PsStrings.replaceOneByOne('a{}b{}c{}', '{}', [1,2]), 'a1b2c{}');
    deepEqual(PsStrings.replaceOneByOne('a{}b{}c', '{}', [1,2,3]), 'a1b2c');
    deepEqual(PsStrings.replaceOneByOne('аубувугу', 'у', [1,2,3]), 'а1б2в3гу');
    deepEqual(PsStrings.replaceOneByOne('аубувугу', 'у', [1,2,3,4,5]), 'а1б2в3г4');

    deepEqual(PsStrings.replaceOneByOne('a{}b{}c', '{}', [1,2,3], function(item) {
        return {
            take: item!=2,
            item: '['+item+']'
        }
    }), 'a[1]b[3]c');

    deepEqual(PsStrings.replaceOneByOne('a{}b{}c{}', '{}', [1,2,3], function(item) {
        return {
            take: item!=2,
            item: '['+item+']'
        }
    }), 'a[1]b[3]c{}');

    deepEqual(PsStrings.replaceOneByOne('аубувугу', 'у', [1,2,3,4,5], function (item) {
        return false;
    }), 'аубувугу');
    deepEqual(PsStrings.replaceOneByOne('аубувугу', 'у', [1,2,3,4,5], function (item) {
        return item!=3;
    }), 'а1б2в4г5');
    deepEqual(PsStrings.replaceOneByOne('аубувугу', 'у', [1,2,3,4,5], function (item) {
        return {
            take: true,
            item: ++item
        };
    }), 'а2б3в4г5');
});

QUnit.test('removeSpaces', function() {
    deepEqual(PsStrings.removeSpaces(' a  b c '), 'abc');
    deepEqual(PsStrings.removeSpaces(' ф ы в а   о л д   ж  '), 'фываолдж');
    deepEqual(PsStrings.removeSpaces(null), '');
    deepEqual(PsStrings.removeSpaces(false), 'false');
});

QUnit.test('padLeft', function() {
    deepEqual(PsStrings.padLeft('a', 'x', 0), 'a');
    deepEqual(PsStrings.padLeft('a', 'x', 1), 'a');
    deepEqual(PsStrings.padLeft('a', 'x', 2), 'xa');
    deepEqual(PsStrings.padLeft('a', 'x', 3), 'xxa');
    deepEqual(PsStrings.padLeft(' a', 'x', 3), 'x a');
    deepEqual(PsStrings.padLeft(' a ', 'x', 3), ' a ');
    deepEqual(PsStrings.padLeft(' a ', 'x', 4), 'x a ');
});


QUnit.test('padRight', function() {
    deepEqual(PsStrings.padRight('a', 'x', 0), 'a');
    deepEqual(PsStrings.padRight('a', 'x', 1), 'a');
    deepEqual(PsStrings.padRight('a', 'x', 2), 'ax');
    deepEqual(PsStrings.padRight('a', 'x', 3), 'axx');
    deepEqual(PsStrings.padRight('a ', 'x', 3), 'a x');
    deepEqual(PsStrings.padRight(' a ', 'x', 3), ' a ');
    deepEqual(PsStrings.padRight(' a ', 'x', 4), ' a x');
});


QUnit.test('fill', function() {
    deepEqual(PsStrings.fill('a', 3), 'aaa');
    deepEqual(PsStrings.fill('a', -1), '');
    deepEqual(PsStrings.fill('ax', 3), 'axa');
    deepEqual(PsStrings.fill('axa', 3), 'axa');
    deepEqual(PsStrings.fill('0', 3), '000');
});


QUnit.test('regExpQuantifier', function() {
    deepEqual(PsStrings.regExpQuantifier('a[]'), 'a\\[\\]');
    deepEqual(PsStrings.regExpQuantifier('a[{}]'), 'a\\[\\{\\}\\]');
});

