/*********************/
QUnit.module('PsMathCore');

QUnit.test('CALC_ACCURACY', function() {
    ok(PsIs.integer(PsMathCore.CALC_ACCURACY));
    ok(PsMathCore.CALC_ACCURACY > 10);
});

QUnit.test('MIN_NUMBER', function() {
    deepEqual(PsMathCore.MIN_NUMBER, 1*('0.'+PsStrings.padLeft('1', '0', PsMathCore.CALC_ACCURACY)));
    deepEqual(PsMathCore.MIN_NUMBER.toFixed(PsMathCore.CALC_ACCURACY), '0.'+PsStrings.padLeft('1', '0', PsMathCore.CALC_ACCURACY));
});


QUnit.test('splitDecimal', function() {
    var doTest = function(input, expected) {
        var actual = PsMathCore.splitDecimal(input);
        deepEqual(actual, expected, 'PsMathCore.splitDecimal('+input+')='+PsObjects.toString(actual));
    }
    
    doTest('-0.0000000', {
        c: '0',
        d: ''
    });
    
    doTest('a', null);
    doTest(1, {
        c: '1',
        d: ''
    });
    doTest(1.123, {
        c: '1',
        d: '123'
    });
    doTest(-1.123, {
        c: '-1',
        d: '123'
    });
    doTest(1.12300, {
        c: '1',
        d: '123'
    });
    doTest(123.999999, {
        c: '123',
        d: '999999'
    });
    doTest('0.11111111111111111111111', {
        c: '0',
        d: PsStrings.fill(1, PsMathCore.CALC_ACCURACY+1)
    });
    doTest('-0.12300000', {
        c: '-0',
        d: '123'
    });
});


QUnit.test('decimalAccuracy', function() {
    deepEqual(PsMathCore.decimalAccuracy('1'), 0);
    deepEqual(PsMathCore.decimalAccuracy('1.123'), 3);
    deepEqual(PsMathCore.decimalAccuracy('1.12300'), 3);
    deepEqual(PsMathCore.decimalAccuracy('1.123001'), 6);
    deepEqual(PsMathCore.decimalAccuracy('0.000'), 0);
    deepEqual(PsMathCore.decimalAccuracy('0.000 00 00'), 0);
    deepEqual(PsMathCore.decimalAccuracy(null), 0);
    deepEqual(PsMathCore.decimalAccuracy(-1.231), 3);
    deepEqual(PsMathCore.decimalAccuracy(-1.231000), 3);
    deepEqual(PsMathCore.decimalAccuracy(-1.231001), 6);
    deepEqual(PsMathCore.decimalAccuracy(PsCore.MIN_NUMBER), PsCore.CALC_ACCURACY);
    deepEqual(PsMathCore.decimalAccuracy(1.23e-10), 12);
    ok(PsMathCore.decimalAccuracy(Math.PI)<=PsMathCore.CALC_ACCURACY);
    ok(PsMathCore.decimalAccuracy(Math.E)<=PsMathCore.CALC_ACCURACY);
    ok(PsMathCore.decimalAccuracy(0.1+0.2)<=PsMathCore.CALC_ACCURACY);
});


QUnit.test('normalize', function() {
    var doTest = function(num, expected) {
        var actual = PsMathCore.normalize(num);
        deepEqual(actual, expected, 'PsMathCore.normalize('+num+')='+actual+', expected='+expected);
    }
    
    var n, num;
    
    //Если число равно точности вычислений - с ним ничего не происходит
    //#Тест на ...000n
    for(n=0; n<=9;n++) {
        num = '1.' + PsStrings.padLeft(n, '0', PsMathCore.CALC_ACCURACY);
        doTest(num, 1*num);
    }
    //#Тест на ...999n
    for(n=0; n<=9;n++) {
        num = '1.' + PsStrings.padLeft(n, '9', PsMathCore.CALC_ACCURACY);
        doTest(num, 1*num);
    }
    
    //Если число превышает точность вычислений - на него влияет последний знак.
    //Но есть после округления дробная часть сильно не сокращается, то число просто отбрасывается.
    //#Тест на ...000n
    for(n=0; n<=9;n++) {
        num = '1.' + PsStrings.padLeft(n, '0', PsMathCore.CALC_ACCURACY+1);
        doTest(num, n<5 ? 1 : 1*(num.removeLastChar(2)+'1'));
    }
    //#Тест на ...999n
    for(n=0; n<=9;n++) {
        //
        num = '1.' + PsStrings.padLeft(n, '9', PsMathCore.CALC_ACCURACY+1);
        doTest(num, n<5 ? 1*num.removeLastCharIf(''+n) : 2);
    }
    
    doTest('0.1', 0.1);
    doTest('0.2', 0.2);
    doTest('0.2', 0.2);
    doTest(0.1+0.2, 0.3);
    doTest(Math.sin(Math.PI), 0);
    doTest(Math.cos(Math.PI/2), 0);
    
    var tests = [
    [0,0], [1,1], [-1,-1],
    [null,null], [undefined,undefined], 
    [Number.NEGATIVE_INFINITY,Number.NEGATIVE_INFINITY], [Number.POSITIVE_INFINITY,Number.POSITIVE_INFINITY], 
    [Number.MAX_VALUE, Number.MAX_VALUE], [Number.MIN_VALUE, 0]
    ];
    
    tests.walk(function(test) {
        doTest(test[0], test[1]);
    });
    
    var accuracy = PsMathCore.CALC_ACCURACY, i, j, pos, neg;
    
    //Нули
    for(i=0; i<=accuracy*2; i++) {
        pos = '0.';
        neg = '-0.';
        for (j=0; j<i; j++) {
            pos+='0';
            neg+='0';
        }
        pos+='1';
        neg+='1';
        
        doTest(pos, pos.split('.')[1].length>accuracy ? 0 : pos*1);
        doTest(neg, neg.split('.')[1].length>accuracy ? 0 : neg*1);
    }
    
    //Девятки
    for(i=1; i<=accuracy*2; i++) {
        pos = '0.';
        neg = '-0.';
        for (j=0; j<i; j++) {
            pos+='9';
            neg+='9';
        }
        doTest(pos, pos.split('.')[1].length>accuracy ? 1 : pos*1);
        doTest(neg, neg.split('.')[1].length>accuracy ? -1 : neg*1);
    }
});


/*********************/
QUnit.module('PsMath');

QUnit.test('round', function() {
    deepEqual(PsMath.round(1, 2), 1);
    deepEqual(PsMath.round(1.2, 2), 1.2);
    deepEqual(PsMath.round(1.23, 2), 1.23);
    deepEqual(PsMath.round(1.234, 2), 1.23);
    deepEqual(PsMath.round(-1.234, 1), -1.2);
    deepEqual(PsMath.round(-1.234, 2), -1.23);
    deepEqual(PsMath.round(-1.234, 3), -1.234);
    deepEqual(PsMath.round(1.4999999, 3), 1.5);
    deepEqual(PsMath.round(1.4991, 3), 1.499);
    deepEqual(PsMath.round(1.4000001, 3), 1.4);
    deepEqual(PsMath.round(1.4009001, 3), 1.401);
    
    for (var i=0; i<1000; i++) {
        //Проверим, что на больших числах всегда одинаковый результат
        deepEqual(PsMath.round(1.23456789123456789, 6), 1.234568);
    }
    
    for (var j=0; j<1000; j++) {
        //Проверим, что на больших числах всегда одинаковый результат
        deepEqual(PsMath.round(-1.23456789123456789, 6), -1.234568);
    }

});

QUnit.test('isnat', function() {
    ok(!PsMath.isnat(-2));
    ok(!PsMath.isnat(-1));
    ok(!PsMath.isnat(0));
    ok(PsMath.isnat(1));
    ok(PsMath.isnat(2));
});

QUnit.test('isprime', function() {
    ok(!PsMath.isprime(-2));
    ok(!PsMath.isprime(-1));
    ok(!PsMath.isprime(0));
    ok(!PsMath.isprime(1));
    ok(PsMath.isprime(2));
    ok(PsMath.isprime(3));
    ok(!PsMath.isprime(4));
    ok(PsMath.isprime(5));
    ok(!PsMath.isprime(6));
    ok(PsMath.isprime(7));
    ok(!PsMath.isprime(8));
    ok(!PsMath.isprime(9));
    ok(!PsMath.isprime(10));
    ok(PsMath.isprime(11));
    ok(!PsMath.isprime(12));
    ok(PsMath.isprime(13));
});

QUnit.test('factorial', function() {
    deepEqual(PsMath.factorial(-2), 1);
    deepEqual(PsMath.factorial(-1), 1);
    deepEqual(PsMath.factorial(0), 1);
    deepEqual(PsMath.factorial(1), 1);
    deepEqual(PsMath.factorial(2), 2);
    deepEqual(PsMath.factorial(2.2), 2);
    deepEqual(PsMath.factorial(3), 6);
    deepEqual(PsMath.factorial(3.3), 6);
    deepEqual(PsMath.factorial(4), 24);
    deepEqual(PsMath.factorial(4.4), 24);
    deepEqual(PsMath.factorial(5), 120);
    deepEqual(PsMath.factorial(5.5), 120);
    deepEqual(PsMath.factorial(6), 720);
    deepEqual(PsMath.factorial(6.6), 720);
    deepEqual(PsMath.factorial(7), 5040);
    deepEqual(PsMath.factorial(7.7), 5040);
});

QUnit.test('pfactorial', function() {
    deepEqual(PsMath.pfactorial(-2), 1);
    deepEqual(PsMath.pfactorial(-1), 1);
    deepEqual(PsMath.pfactorial(0), 1);
    deepEqual(PsMath.pfactorial(1), 1);
    deepEqual(PsMath.pfactorial(2), 2);
    deepEqual(PsMath.pfactorial(2.2), 2);
    deepEqual(PsMath.pfactorial(3), 6);
    deepEqual(PsMath.pfactorial(3.3), 6);
    deepEqual(PsMath.pfactorial(4), 6);
    deepEqual(PsMath.pfactorial(4.4), 6);
    deepEqual(PsMath.pfactorial(5), 30);
    deepEqual(PsMath.pfactorial(5.5), 30);
    deepEqual(PsMath.pfactorial(6), 30);
    deepEqual(PsMath.pfactorial(6.6), 30);
    deepEqual(PsMath.pfactorial(7), 210);
    deepEqual(PsMath.pfactorial(7.7), 210);
});


QUnit.test('primes', function() {
    deepEqual(PsMath.primes(-2), []);
    deepEqual(PsMath.primes(-1), []);
    deepEqual(PsMath.primes(1), []);
    deepEqual(PsMath.primes(2), [2]);
    deepEqual(PsMath.primes(3), [2,3]);
    deepEqual(PsMath.primes(4), [2,3]);
    deepEqual(PsMath.primes(5), [2,3,5]);
    deepEqual(PsMath.primes(6), [2,3,5]);
    deepEqual(PsMath.primes(7), [2,3,5,7]);
    //...
    deepEqual(PsMath.primes(33), [2,3,5,7,11,13,17,19,23,29,31]);
});

QUnit.test('primepi', function() {
    deepEqual(PsMath.primepi(-2), 0);
    deepEqual(PsMath.primepi(-1), 0);
    deepEqual(PsMath.primepi(1), 0);
    deepEqual(PsMath.primepi(2), 1);
    deepEqual(PsMath.primepi(3), 2);
    deepEqual(PsMath.primepi(4), 2);
    deepEqual(PsMath.primepi(5), 3);
    deepEqual(PsMath.primepi(6), 3);
    deepEqual(PsMath.primepi(7), 4);
    //...
    deepEqual(PsMath.primepi(33), [2,3,5,7,11,13,17,19,23,29,31].length);
});

QUnit.test('relprime', function() {
    deepEqual(PsMath.relprime(2), [1]);
    deepEqual(PsMath.relprime(3), [1,2]);
    deepEqual(PsMath.relprime(4), [1,3]);
    deepEqual(PsMath.relprime(5), [1,2,3,4]);
    deepEqual(PsMath.relprime(6), [1,5]);
    deepEqual(PsMath.relprime(7), [1,2,3,4,5,6]);
    deepEqual(PsMath.relprime(8), [1,3,5,7]);
    deepEqual(PsMath.relprime(9), [1,2,4,5,7,8]);
    deepEqual(PsMath.relprime(10), [1,3,7,9]);
});

QUnit.test('totient', function() {
    for(var i=-5; i<500; i++) {
        deepEqual(PsMath.totient(i), PsMath.relprime(i).length);
    }
});

QUnit.test('factor', function() {
    deepEqual(PsMath.factor(2), [2]);
    deepEqual(PsMath.factor(3), [3]);
    deepEqual(PsMath.factor(4), [2,2]);
    deepEqual(PsMath.factor(5), [5]);
    deepEqual(PsMath.factor(6), [2,3]);
    deepEqual(PsMath.factor(7), [7]);
    deepEqual(PsMath.factor(8), [2,2,2]);
    deepEqual(PsMath.factor(9), [3,3]);
    deepEqual(PsMath.factor(10), [2,5]);
    deepEqual(PsMath.factor(11), [11]);
    deepEqual(PsMath.factor(12), [2,2,3]);
    deepEqual(PsMath.factor(13), [13]);
    deepEqual(PsMath.factor(14), [2,7]);
    deepEqual(PsMath.factor(15), [3,5]);
    deepEqual(PsMath.factor(16), [2,2,2,2]);
    deepEqual(PsMath.factor(17), [17]);
    deepEqual(PsMath.factor(18), [2,3,3]);
    deepEqual(PsMath.factor(19), [19]);
    deepEqual(PsMath.factor(20), [2,2,5]);
    deepEqual(PsMath.factor(21), [3,7]);
});

QUnit.test('sq', function() {
    deepEqual(PsMath.sq(0.1), 0.01);
    deepEqual(PsMath.sq(0.2), 0.04);
    deepEqual(PsMath.sq(0.3), 0.09);
    deepEqual(PsMath.sq(0.4), 0.16);
    for(var i=-500; i<500; i+=0.5) {
        deepEqual(PsMath.sq(i), i*i);
    }
});

QUnit.test('dist', function() {
    deepEqual(PsMath.dist(3, 4), 5);
    deepEqual(PsMath.dist([0,0], [5,0]), 5);
    deepEqual(PsMath.dist([0,0], [0,5]), 5);
    deepEqual(PsMath.dist([1,0], [0,0]), 1);
    deepEqual(PsMath.dist([0,2], [0,0]), 2);
    deepEqual(Math.round(PsMath.dist(5, 6)), Math.round(Math.sqrt(25+36)));
    deepEqual(Math.round(PsMath.dist([1,2], [-2,-5])), Math.round(Math.sqrt(9+49)));
});

QUnit.test('fibonacci', function() {
    deepEqual(PsMath.fibonacci(0), 0);
    deepEqual(PsMath.fibonacci(1), 1);
    deepEqual(PsMath.fibonacci(2), 1);
    deepEqual(PsMath.fibonacci(3), 2);
    
    
    var arr = [];
    for(var i=0; i<=21; i++) {
        arr.push(PsMath.fibonacci(i));
    }
    deepEqual(arr, [0, 1, 1, 2, 3, 5, 8, 13, 21, 34, 55, 89, 144, 233, 377, 610, 987, 1597, 2584, 4181, 6765, 10946]);
});

QUnit.test('sign', function() {
    deepEqual(PsMath.sign(-100500), -1);
    deepEqual(PsMath.sign(-1.1), -1);
    deepEqual(PsMath.sign(0), 0);
    deepEqual(PsMath.sign(1.1), 1);
    deepEqual(PsMath.sign(100500), 1);
});

QUnit.test('isSameSign', function() {
    ok(PsMath.isSameSign([]));
    ok(PsMath.isSameSign(2,4));
    ok(PsMath.isSameSign(-2,-4));
    ok(PsMath.isSameSign(0,0));
    ok(!PsMath.isSameSign(-2,4));
    ok(!PsMath.isSameSign(0,4));
    ok(!PsMath.isSameSign(-2,0));
    ok(PsMath.isSameSign([1,2,4]));
    ok(PsMath.isSameSign([-1,-2,-4]));
    ok(PsMath.isSameSign([0,0,0]));
    ok(!PsMath.isSameSign([-1,0,2]));
    ok(!PsMath.isSameSign([-1,2]));
    ok(!PsMath.isSameSign([3,-2]));
});

QUnit.test('max', function() {
    deepEqual(PsMath.max(1,2), 2);
    deepEqual(PsMath.max(1,-2), 1);
    deepEqual(PsMath.max([1,-2,3]), 3);
    deepEqual(PsMath.max([1]), 1);
});

QUnit.test('min', function() {
    deepEqual(PsMath.min(1,2), 1);
    deepEqual(PsMath.min(1,-2), -2);
    deepEqual(PsMath.min([1,-2,3]), -2);
    deepEqual(PsMath.min([1]), 1);
});

QUnit.test('sum', function() {
    deepEqual(PsMath.sum([1,2]), 3);
    deepEqual(PsMath.sum([1,-2]), -1);
    deepEqual(PsMath.sum([1,3]), 4);
    deepEqual(PsMath.sum([1,3,-4]), 0);
    deepEqual(PsMath.sum(1,2,3), 6);
    deepEqual(PsMath.sum(0.1,0.2), 0.3);
    deepEqual(PsMath.sum(0.01,0.02, 0.3), 0.33);
    deepEqual(PsMath.sum(PsMath.sin(Math.PI), PsMath.sin(Math.PI/6)), 0.5);
    deepEqual(PsMath.sum(PsMath.sin(2*Math.PI), PsMath.sin(Math.PI/6)), 0.5);
    deepEqual(PsMath.sum(PsMathCore.MIN_NUMBER, PsMathCore.MIN_NUMBER), 2*PsMathCore.MIN_NUMBER);
    deepEqual(PsMath.sum(2*PsMathCore.MIN_NUMBER, PsMathCore.MIN_NUMBER, [PsMathCore.MIN_NUMBER]), 4*PsMathCore.MIN_NUMBER);
    
    var fill = function(num, delta) {
        return PsStrings.fill(num, PsMathCore.CALC_ACCURACY+PsUtil.toInteger(delta, 0))
    }
    
    var maxNum0__ = '0.' + fill('0', -2);
    deepEqual(PsMath.sum(maxNum0__+'1', maxNum0__+'2', maxNum0__+'3'), 1*(maxNum0__+'6'));
    deepEqual(PsMath.sum(maxNum0__+'01', maxNum0__+'02', [maxNum0__+'05', maxNum0__+'06']), 1*(maxNum0__ + '14'));
    
    var n999__ = fill('9', -2);
    var maxNum9__ = '0.' + n999__;
    deepEqual(PsMath.sum(maxNum9__+'1', maxNum9__+'2', maxNum9__+'3'), (1*(n999__+'1') + 1*(n999__+'2') + 1*(n999__+'3'))/Math.pow(10, PsMathCore.CALC_ACCURACY-1));
    deepEqual(PsMath.sum(maxNum9__+'01', maxNum9__+'02', [maxNum9__+'05', maxNum9__+'06']), 
        (1*(n999__ + '01') + 1*(n999__ + '02') + 1*(n999__ + '05') + 1*(n999__ + '06'))/Math.pow(10, PsMathCore.CALC_ACCURACY));
});

QUnit.test('average', function() {
    deepEqual(PsMath.average(), NaN);
    deepEqual(PsMath.average(null), NaN);
    deepEqual(PsMath.average(undefined), NaN);
    deepEqual(PsMath.average([1,2]), 1.5);
    deepEqual(PsMath.average([1,3]), 2);
    deepEqual(PsMath.average([1,2,3]), 2);
    deepEqual(PsMath.average([1,-2,7]), 2);
    deepEqual(PsMath.average([1,2,-3]), 0);
    deepEqual(PsMath.average(1,2,3), 2);
    deepEqual(PsMath.average(1,2,3, [4,5]), PsMath.normalize((1+2+3+4+5)/5));
    deepEqual(PsMath.average(1,2,3, [4,5,[6]]), PsMath.normalize((1+2+3+4+5+6)/6));
    deepEqual(PsMath.average(0.1,0.2,0.3), 0.2);
});

QUnit.test('product', function() {
    deepEqual(PsMath.product([1,2]), 2);
    deepEqual(PsMath.product([1,2,3]), 6);
    deepEqual(PsMath.product([-1,2,3]), -6);
    deepEqual(PsMath.product([0,-1,2,3]), 0);
    deepEqual(PsMath.product(-1,2,3), -6);
    deepEqual(PsMath.product(-1,2,3, [4, [5]]), -120);
    deepEqual(PsMath.product(0.1, 0.2), 0.02);
    deepEqual(PsMath.product(0.01, 0.02), 0.0002);
});

QUnit.test('closestTo', function() {
    deepEqual(PsMath.closestTo(1), 1);
    deepEqual(PsMath.closestTo(-100), -100);
    deepEqual(PsMath.closestTo(2, [1,3]), 1);
    deepEqual(PsMath.closestTo(2, [1,4]), 1);
    deepEqual(PsMath.closestTo(2, [1,2.5]), 2.5);
    deepEqual(PsMath.closestTo(-2, [2.5, -2.5]), -2.5);
    deepEqual(PsMath.closestTo(-2, [-3.5, -2.5]), -2.5);
    deepEqual(PsMath.closestTo(-2, [-2.5, -1.5]), -2.5);
    deepEqual(PsMath.closestTo(-2, [-2.7, -1.5]), -1.5);
});

QUnit.test('num2bounds', function() {
    deepEqual(PsMath.num2bounds(1, [1,2]), 1);
    deepEqual(PsMath.num2bounds(2, [1,3]), 2);
    deepEqual(PsMath.num2bounds(4, [1,3]), 3);
    deepEqual(PsMath.num2bounds(0, [1,3]), 1);
    deepEqual(PsMath.num2bounds(4, [1,3,5]), 4);
    deepEqual(PsMath.num2bounds(6, [1,3,5]), 5);
    deepEqual(PsMath.num2bounds(0, [1,3,5]), 1);
    deepEqual(PsMath.num2bounds(0, [-1,3,5]), 0);
    deepEqual(PsMath.num2bounds(-1, [-1,3,5]), -1);
    deepEqual(PsMath.num2bounds(5, [-1,3,5]), 5);
    deepEqual(PsMath.num2bounds(-5, [-1,-3,5]), -3);
    deepEqual(PsMath.num2bounds(6, [-1,7,5]), 6);
    deepEqual(PsMath.num2bounds(6, [5,2,-6]), 5);
});

QUnit.test('gcd', function() {
    deepEqual(PsMath.gcd([1,2,3]), 1);
    deepEqual(PsMath.gcd([2,4]), 2);
    deepEqual(PsMath.gcd([2,5]), 1);
    deepEqual(PsMath.gcd([3,9]), 3);
    deepEqual(PsMath.gcd([3,6,9]), 3);
    deepEqual(PsMath.gcd([6,9]), 3);
    deepEqual(PsMath.gcd([69,9]), 3);
    deepEqual(PsMath.gcd([69,9,12]), 3);
    deepEqual(PsMath.gcd([7,21]), 7);
    deepEqual(PsMath.gcd([8,16]), 8);
    deepEqual(PsMath.gcd([12,18,9]), 3);
    deepEqual(PsMath.gcd([9,63]), 9);
    deepEqual(PsMath.gcd([10,60]), 10);
    deepEqual(PsMath.gcd([6,12,18]), 6);
    deepEqual(PsMath.gcd([6,12,18,21]), 3);
});

QUnit.test('lcm', function() {
    deepEqual(PsMath.lcm([1,2,3]), 6);
    deepEqual(PsMath.lcm([2,4]), 4);
    deepEqual(PsMath.lcm([2,5]), 10);
    deepEqual(PsMath.lcm([3,9]), 9);
    deepEqual(PsMath.lcm([3,6,9]), 18);
    deepEqual(PsMath.lcm([6,9]), 18);
    deepEqual(PsMath.lcm([9,9]), 9);
    deepEqual(PsMath.lcm([9,12]), 36);
    deepEqual(PsMath.lcm([7,21]), 21);
    deepEqual(PsMath.lcm([8,16]), 16);
    deepEqual(PsMath.lcm([12,18,9]), 36);
    deepEqual(PsMath.lcm([9,63]), 63);
    deepEqual(PsMath.lcm([10,60]), 60);
    deepEqual(PsMath.lcm([6,12,18]), 36);
    deepEqual(PsMath.lcm([6,12,18,21]), 252);
});

QUnit.test('ln', function() {
    for(var i=0; i<100; i++) {
        deepEqual(PsMath.ln(i), Math.log(i));
        var num = Math.random();
        deepEqual(PsMath.ln(num), Math.log(num));
    }
    deepEqual(PsMath.ln(1), 0);
    deepEqual(PsMath.ln(Math.E), 1);
    deepEqual(PsMath.ln(-2), NaN);
});

QUnit.test('lg', function() {
    deepEqual(PsMath.lg(1), 0);
    deepEqual(PsMath.lg(10), 1);
    deepEqual(PsMath.lg(100), 2);
    deepEqual(PsMath.lg(-2), NaN);
});

QUnit.test('log', function() {
    var testLog = function(x,b,expected) {
        var result = PsMath.log(x,b);
        deepEqual(result, expected, 'log_'+b+'('+x+')='+result+', expected: ' + expected);
    }
    
    testLog(1,2, 0);
    testLog(-1,3, NaN);
    testLog(1,-3, NaN);
    testLog(1,2, 0);
    testLog(1,3, 0);
    testLog(8,2, 3);
    testLog(8,8, 1);
    testLog(9,3, 2);
    
    for(var i=0; i<100; i++) {
        deepEqual(PsMath.log(i), Math.log(i), 'log('+i+')=ln('+i+')='+Math.log(i));
        var num = Math.random();
        deepEqual(PsMath.log(num), Math.log(num), 'log('+num+')=ln('+num+')='+Math.log(num));
        if(i>2) {
            testLog(i*i,i, 2);
            testLog(1,i, 0);
        }
    }
});

QUnit.test('ank', function() {
    var test = function(n,k,expected) {
        var result = PsMath.ank(n,k);
        deepEqual(result, expected, 'A_'+n+'=^'+k+'='+result+', expected: ' + expected);
    }
    
    test(5,1,5);
    test(5,2,20);
    test(3,3,6);
    test(2,2,2);
    test(8,4,1680);
    test(8,2,56);
    test(8,1,8);
});


QUnit.test('cnk', function() {
    var test = function(n,k,expected) {
        var result = PsMath.cnk(n,k);
        deepEqual(result, expected, 'C_'+n+'=^'+k+'='+result+', expected: ' + expected);
    }
    
    test(3,1,3);
    test(3,2,3);
    test(3,3,1);
    test(4,2,6);
    test(6,4,15);
    test(6,5,6);
    test(6,6,1);
    test(6,4,15);
});


QUnit.test('base', function() {
    var test = function(x,a,b, expected) {
        var result = PsMath.base(x,a,b);
        deepEqual(result, expected, 'PsMath.base('+x+', '+a+', '+b+')='+result+', expected: ' + expected);
    }
    
    var test10 = function(x,a, expected) {
        var result = PsMath.base(x,a);
        deepEqual(result, expected, 'PsMath.base('+x+', '+a+')='+result+', expected: ' + expected);
    }
    
    test10(10,10,10);
    test10(10,8,12);
    test10(10,4,22);
    test10(10,2,1010);
    test10(4,2,100);
    test10(10,5,20);
});
/*
QUnit.test('add', function() {
    var doAdd = function(a, b, expected) {
        var actual = PsMath.add(a, b);
        deepEqual(actual, expected, 'PsMath.add('+a+', '+b+')='+actual);
    }

    var minNumber = 1*('0.'+PsStrings.padLeft('', '0', PsCore.CALC_ACCURACY-1)+'1');
    deepEqual(minNumber, PsCore.MIN_NUMBER, 'minNumber='+minNumber);

    doAdd(0.1, 0.2, 0.3);
    doAdd(0.0001, 0.2, 0.2001);
    doAdd(minNumber, 0.2, 1*('0.2'+PsStrings.padLeft('', '0', PsCore.CALC_ACCURACY-2)+'1'));
    doAdd(minNumber, 0.003, 1*('0.003'+PsStrings.padLeft('', '0', PsCore.CALC_ACCURACY-4)+'1'));
    doAdd(minNumber, minNumber, 1*('0.'+PsStrings.padLeft('', '0', PsCore.CALC_ACCURACY-1)+'2'));
    doAdd(minNumber.toFixed(PsCore.CALC_ACCURACY)+'2', minNumber.toFixed(PsCore.CALC_ACCURACY)+'5', 1*('0.'+PsStrings.padLeft('', '0', PsCore.CALC_ACCURACY-1)+'3'));
    doAdd(0.0000001, 0.2, 0.2000001);
});
*/

QUnit.test('radToGrad', function() {
    deepEqual(PsMath.radToGrad(0), 0);
    deepEqual(PsMath.radToGrad(Math.PI/2), 90);
    deepEqual(PsMath.radToGrad(Math.PI/3), 60);
    deepEqual(PsMath.radToGrad(Math.PI/4), 45);
    deepEqual(PsMath.radToGrad(Math.PI/6), 30);
    deepEqual(PsMath.radToGrad(Math.PI), 180);
    deepEqual(PsMath.radToGrad(2*Math.PI), 360);
    deepEqual(PsMath.radToGrad(4*Math.PI), 720);
    deepEqual(PsMath.radToGrad(-Math.PI/2), -90);
    deepEqual(PsMath.radToGrad(-Math.PI/3), -60);
    deepEqual(PsMath.radToGrad(-Math.PI/4), -45);
    deepEqual(PsMath.radToGrad(-Math.PI/6), -30);
    deepEqual(PsMath.radToGrad(-Math.PI), -180);
    deepEqual(PsMath.radToGrad(-2*Math.PI), -360);
    deepEqual(PsMath.radToGrad(-4*Math.PI), -720);
});

QUnit.test('gradToRad', function() {
    deepEqual(PsMath.gradToRad(0), 0);
    deepEqual(PsMath.gradToRad(90, true), Math.PI/2);
    deepEqual(PsMath.gradToRad(60, true), Math.PI/3);
    deepEqual(PsMath.gradToRad(45, true), Math.PI/4);
    deepEqual(PsMath.gradToRad(30, true), Math.PI/6);
    deepEqual(PsMath.gradToRad(180, true), Math.PI);
    deepEqual(PsMath.gradToRad(360, true), 2*Math.PI);
    deepEqual(PsMath.gradToRad(720, true), 4*Math.PI);
    deepEqual(PsMath.gradToRad(-90, true), -Math.PI/2);
    deepEqual(PsMath.gradToRad(-60, true), -Math.PI/3);
    deepEqual(PsMath.gradToRad(-45, true), -Math.PI/4);
    deepEqual(PsMath.gradToRad(-30, true), -Math.PI/6);
    deepEqual(PsMath.gradToRad(-180, true), -Math.PI);
    deepEqual(PsMath.gradToRad(-360, true), -2*Math.PI);
    deepEqual(PsMath.gradToRad(-720, true), -4*Math.PI);
    
    deepEqual(PsMath.gradToRad(0), 0);
    deepEqual(PsMath.gradToRad(90), PsMathCore.normalize(Math.PI/2));
    deepEqual(PsMath.gradToRad(60), PsMathCore.normalize(Math.PI/3));
    deepEqual(PsMath.gradToRad(45), PsMathCore.normalize(Math.PI/4));
    deepEqual(PsMath.gradToRad(30), PsMathCore.normalize(Math.PI/6));
    deepEqual(PsMath.gradToRad(180), PsMathCore.normalize(Math.PI));
    deepEqual(PsMath.gradToRad(360), PsMathCore.normalize(2*Math.PI));
    deepEqual(PsMath.gradToRad(720), PsMathCore.normalize(4*Math.PI));
    deepEqual(PsMath.gradToRad(-90), PsMathCore.normalize(-Math.PI/2));
    deepEqual(PsMath.gradToRad(-60), PsMathCore.normalize(-Math.PI/3));
    deepEqual(PsMath.gradToRad(-45), PsMathCore.normalize(-Math.PI/4));
    deepEqual(PsMath.gradToRad(-30), PsMathCore.normalize(-Math.PI/6));
    deepEqual(PsMath.gradToRad(-180), PsMathCore.normalize(-Math.PI));
    deepEqual(PsMath.gradToRad(-360), PsMathCore.normalize(-2*Math.PI));
    deepEqual(PsMath.gradToRad(-720), PsMathCore.normalize(-4*Math.PI));
});

var TEST_ANGLES = {
    SIN: [
    [0,0], [Math.PI, 0], [2*Math.PI, 0], [-Math.PI, 0], [-2*Math.PI, 0],
    [Math.PI/2, 1], [3*Math.PI/2, -1], [-Math.PI/2, -1], [-3*Math.PI/2, 1], 
    [Math.PI/6, 0.5], [Math.PI/4, Math.sqrt(2)/2], [Math.PI/3, Math.sqrt(3)/2],
    [-Math.PI/6, -0.5], [-Math.PI/4, -Math.sqrt(2)/2], [-Math.PI/3, -Math.sqrt(3)/2]
    ],
    COS: [
    [0,1], [Math.PI, -1], [2*Math.PI, 1], [-Math.PI, -1], [-2*Math.PI, 1],
    [Math.PI/2, 0], [3*Math.PI/2, 0], [-Math.PI/2, 0], [-3*Math.PI/2, 0], 
    [Math.PI/6, Math.sqrt(3)/2], [Math.PI/4, Math.sqrt(2)/2], [Math.PI/3, 0.5],
    [-Math.PI/6, Math.sqrt(3)/2], [-Math.PI/4, Math.sqrt(2)/2], [-Math.PI/3, 0.5]
    ],
    TG: function() {
        var TG = [];
        for (var i=0; i<this.SIN.length; i++) {
            var rad = this.SIN[i][0];
            var sin = this.SIN[i][1];
            var cos = this.COS[i][1];
            if (cos==0) {
                TG.push([rad, NaN]);
            } else {
                TG.push([rad, sin/cos]);
            }
        }
        return TG;
    },
    CTG: function() {
        var CTG = [];
        for (var i=0; i<this.SIN.length; i++) {
            var rad = this.SIN[i][0];
            var sin = this.SIN[i][1];
            var cos = this.COS[i][1];
            if (sin==0) {
                CTG.push([rad, NaN]);
            } else {
                CTG.push([rad, cos/sin]);
            }
        }
        return CTG;
    },
    //Косеканс (1/sin)
    CSC: function() {
        var CSC = [];
        for (var i=0; i<this.SIN.length; i++) {
            var rad = this.SIN[i][0];
            var sin = this.SIN[i][1];
            CSC.push([rad, 1/sin]);
        }
        return CSC;
    },
    //Секанс (1/косинус)
    SEC: function() {
        var SEC = [];
        for (var i=0; i<this.COS.length; i++) {
            var rad = this.COS[i][0];
            var cos = this.COS[i][1];
            SEC.push([rad, 1/cos]);
        }
        return SEC;
    },
    
    testTrig: function(arr_rad2val, func, rad2grad, round) {
        arr_rad2val.walk(function(test) {
            var ang = rad2grad ? PsMath.radToGrad(test[0], true) : test[0];
            var expected = PsMathCore.normalize(test[1]);
            var actual = PsMath[func](ang);
            if (round) {
                expected = PsMath.round(expected, PsCore.CALC_ACCURACY-1);
                actual = PsMath.round(actual, PsCore.CALC_ACCURACY-1);
            }
            if (PsIs.number(expected)) {
                deepEqual(actual, expected, 'PsMath.'+func+'('+ang+')='+actual+', expected: ' + expected);
                deepEqual(PsMath[func](ang), PsMathCore.normalize(PsMath[func](ang)));
            } else {
                ok(!PsIs.number(actual), 'PsMath.'+func+'('+ang+')='+actual+', expected: NaN');
            }
        });
    }
}

QUnit.test('sin', function() {
    TEST_ANGLES.testTrig(TEST_ANGLES.SIN, 'sin', false);
});

QUnit.test('cos', function() {
    TEST_ANGLES.testTrig(TEST_ANGLES.COS, 'cos', false);
});


QUnit.test('sinGrad', function() {
    TEST_ANGLES.testTrig(TEST_ANGLES.SIN, 'sinGrad', true);
});

QUnit.test('cosGrad', function() {
    TEST_ANGLES.testTrig(TEST_ANGLES.COS, 'cosGrad', true);
});

QUnit.test('tg', function() {
    TEST_ANGLES.testTrig(TEST_ANGLES.TG(), 'tg', false, true);
});

QUnit.test('ctg', function() {
    TEST_ANGLES.testTrig(TEST_ANGLES.CTG(), 'ctg', false, true);
});

QUnit.test('tgGrad', function() {
    TEST_ANGLES.testTrig(TEST_ANGLES.TG(), 'tgGrad', true, true);
});

QUnit.test('ctgGrad', function() {
    TEST_ANGLES.testTrig(TEST_ANGLES.CTG(), 'ctgGrad', true, true);
});

QUnit.test('csc', function() {
    TEST_ANGLES.testTrig(TEST_ANGLES.CSC(), 'csc', false, true);
});

QUnit.test('sec', function() {
    TEST_ANGLES.testTrig(TEST_ANGLES.SEC(), 'sec', false, true);
});




/*********************/
QUnit.module('PsIntervals');

var doTestIntervals = function(func, params, expected) {
    var actual = PsIntervals[func](params);
    deepEqual(actual, expected, 'PsIntervals.'+func+'('+PsObjects.toString(params)+')='+PsObjects.toString(actual)+', expected: '+PsObjects.toString(expected));
    actual = PsIntervals[func].apply(undefined, params);
    deepEqual(actual, expected, 'PsIntervals.'+func+'('+(PsIs.array(params) ? params.asString().removeFirstChar().removeLastChar() :params) +')='+PsObjects.toString(actual)+', expected: '+PsObjects.toString(expected));
}

QUnit.test('make', function() {
    doTestIntervals('make', undefined, null);
    doTestIntervals('make', [], null);
    doTestIntervals('make', [[1,2], [2,3], 4], [[1,2], [2,3], [4,4]]);
    doTestIntervals('make', [[1,2], [3,4]], [[1,2], [3,4]]);
    doTestIntervals('make', [[1,3], [2,1]], [[1,2], [1,3]]);
    doTestIntervals('make', [[4,3], [2,1]], [[1,2], [3,4]]);
    doTestIntervals('make', [[1,2], [3,4], 5], [[1,2], [3,4], [5,5]]);
    doTestIntervals('make', [[4,2], [3,1]], [[1,3], [2,4]]);
    doTestIntervals('make', [[4,2], [3,1], [7, [-1]]], [[-1,-1],[1,3],[2,4],[7,7]]);
    doTestIntervals('make', [[4,1], [3,1], ['7', [-1, 'a']]], [[-1,-1],[1,3],[1,4],[7,7]]);
    doTestIntervals('make', [[4,2], [3,1], ['7', [-1, '1e+3']]], [[-1,1000],[1,3],[2,4],[7,7]]);
    doTestIntervals('make', [[4,2], [3,1], ['7', [-1, '1e+3', null]]], [[-1,1000],[1,3],[2,4],[7,7]]);
    doTestIntervals('make', [-1.5, [-1]], [[-1.5,-1.5], [-1,-1]]);
    doTestIntervals('make', [1], [[1, 1]]);
    doTestIntervals('make', [2,1], [[1, 2]]);
    doTestIntervals('make', [1, 3, 2], [[1, 3]]);
    doTestIntervals('make', [1, -3, 2], [[-3,2]]);
    doTestIntervals('make', [null, [3, 5, [7, 'a', [-2]]]], [[-2,-2],[3,5],[7,7]]);
    doTestIntervals('make', [[1, 2], [3, 5]], [[1, 2], [3, 5]]);
    doTestIntervals('make', [[1, 2, 7], [3, 5], 6, 'a'], [[1, 7],[3,5],[6,6]]);
    doTestIntervals('make', [[1, 2, -7], [3, 5], -6, 'a'], [[-7, 2],[-6,-6],[3,5]]);
    doTestIntervals('make', [[1, 2, -7], [3, 5], -6, 'a', 10, [['-10']]], [[-10,-10],[-7,2],[-6,10],[3,5]]);
});

QUnit.test('makeSingle', function() {
    doTestIntervals('makeSingle', undefined, null);
    doTestIntervals('makeSingle', [], null);
    doTestIntervals('makeSingle', [5], [5, 5]);
    doTestIntervals('makeSingle', [[1,2], [2,3], 4], [1, 4]);
    doTestIntervals('makeSingle', [[1,2], [3,4]], [1,4]);
    doTestIntervals('makeSingle', [[1,3], [2,1]], [1,3]);
    doTestIntervals('makeSingle', [[4,3], [2,1], 5], [1,5]);
    doTestIntervals('makeSingle', [[4,2], [3,1], [7, [-1]]], [-1, 7]);
    doTestIntervals('makeSingle', [[4,1], [3,1], ['7', [-1, 'a']]], [-1,7]);
    doTestIntervals('makeSingle', [[4,2], [3,1], ['7', [-1, '1e+3']]], [-1,1000]);
    doTestIntervals('makeSingle', [[4,2], [3,1], ['7', [-1, '-1e+3', null]]], [-1000,7]);
    doTestIntervals('makeSingle', [-1.5, [-1]], [-1.5, -1]);
    doTestIntervals('makeSingle', [1], [1, 1]);
    doTestIntervals('makeSingle', [[1, 2, -7], [3, 5], -6, 'a', 10, [['-10']]], [-10,10]);
});

QUnit.test('union', function() {
    doTestIntervals('union', undefined, null);
    doTestIntervals('union', [], null);
    doTestIntervals('union', [[1,2], [2,3], 4], [[1,3], [4,4]]);
    doTestIntervals('union', [1,2,Math.PI, [Math.E, 1]], [[1, PsMathCore.normalize(Math.PI)]]);
    doTestIntervals('union', [[3,Math.E], [4, Math.PI]], [[PsMathCore.normalize(Math.E), 3], [PsMathCore.normalize(Math.PI), 4]]);
    doTestIntervals('union', [1,2,-3], [[-3,2]]);
    doTestIntervals('union', [1,2,-3], [[-3,2]]);
    doTestIntervals('union', [[1,2], [3,4], 5], [[1,2], [3,4], [5,5]]);
    doTestIntervals('union', [1, 2, -3, [4,5]], [[-3, 2], [4,5]]);
    doTestIntervals('union', [1, 2, -3, [4,5, [8,9]]], [[-3, 2], [4,5], [8,9]]);
    doTestIntervals('union', [[1,2], [1,2]], [[1,2]]);
    doTestIntervals('union', [[1,2], [], [1,2]], [[1,2]]);
    doTestIntervals('union', [[1,2], [-3], [1,2]], [[-3, -3], [1,2]]);
    doTestIntervals('union', [[1,2], [], [3,2]], [[1,3]]);
    doTestIntervals('union', [[2,-2], [3,4], [6,-1]], [[-2,6]]);
    doTestIntervals('union', [[-3,2], ['8',-1], [3,4]], [[-3,8]]);
    doTestIntervals('union', [[2,2], [1,1], [3,3]], [[1,1], [2,2], [3,3]]);
    doTestIntervals('union', [3,-1,2], [[-1,3]]);
    doTestIntervals('union', [[2,3], [0,-1, -2], [3,5]], [[-2,0], [2,5]]);
    doTestIntervals('union', [[2,3], [0,-1, -2,-3], [-3,-5]], [[-5,0], [2,3]]);
    doTestIntervals('union', [[2,3,0], [0,-1, -2,-3], [-3,-5],[3,4]], [[-5,4]]);
    doTestIntervals('union', [1,2,[2,5,[5,6],[7,-1]]], [[-1,7]]);
    doTestIntervals('union', [1,2,[2,5,[5,6],['7',8, 'a']]], [[1,6],[7,8]]);
    doTestIntervals('union', [1,2,[2,5,[5,6],[7,8]],7.5], [[1,8]]);
    doTestIntervals('union', [1,2,[4,5],3], [[1,3],[4,5]]);
    doTestIntervals('union', [[4,5], -1,2,3], [[-1,3],[4,5]]);
    doTestIntervals('union', [[4,5], '-1',2,3,'x'], [[-1,3],[4,5]]);
    doTestIntervals('union', ['x',[2,-1],['5','1e+2']], [[-1,2],[5,1e+2]]);
});

QUnit.test('intersect', function() {
    doTestIntervals('intersect', undefined, null);
    doTestIntervals('intersect', [], null);
    doTestIntervals('intersect', [[1,2], [2,3]], [2, 2]);
    doTestIntervals('intersect', [[1,2], [2,3], 4], null);
    doTestIntervals('intersect', [[1,2,Math.PI], [Math.E, 1]], [1, PsMathCore.normalize(Math.E)]);
    doTestIntervals('intersect', [[3,Math.E], [4, Math.PI]], null);
    doTestIntervals('intersect', [[2,Math.PI], [4, Math.E]], [PsMathCore.normalize(Math.E), PsMathCore.normalize(Math.PI)]);
    doTestIntervals('intersect', [1,2,-3], [-3,2]);
    doTestIntervals('intersect', [[1,2], [3,4], 5], null);
    doTestIntervals('intersect', [[1,2], [2,3]], [2,2]);
    doTestIntervals('intersect', [1, 2, -3, [4,5]], null);
    doTestIntervals('intersect', [1, 2, -3, [4,5, [8,9]]], null);
    doTestIntervals('intersect', [[1,2], [1,2]], [1,2]);
    doTestIntervals('intersect', [[1,2], [], [1,2]], [1,2]);
    doTestIntervals('intersect', [[1,2], [-3], [1,2]], null);
    doTestIntervals('intersect', [[1,2], [], [3,2]], [2,2]);
    doTestIntervals('intersect', [[2,-2], [3,-4], [-6,-1]], [-2,-1]);
    doTestIntervals('intersect', [[-3,2], ['-8',-1], [-3,4]], [-3,-1]);
    doTestIntervals('intersect', [[2,2], [1,1], [3,3]], null);
    doTestIntervals('intersect', [[3,-1,2], 1], [1,1]);
    doTestIntervals('intersect', [[2,-3], [0,-1, -2]], [-2,0]);
    doTestIntervals('intersect', [[2,-3], [0,-1, -2,-3], [-3,-5]], [-3,-3]);
    doTestIntervals('intersect', [[2,3,0], [0,-1, -2,-3], [3,-5],[-3,4]], [0,0]);
    doTestIntervals('intersect', [[1,5], [5, -8], [2,6],[9,-1e+10]], [2,5]);
    doTestIntervals('intersect', [1,2,[2,5,[5,6]]], null);
    doTestIntervals('intersect', [[1,4], [2,3]], [2,3]);
    doTestIntervals('intersect', [1,2,[4,5],3], null);
    doTestIntervals('intersect', [[4,5], -1,2,3,7], [4,5]);
    doTestIntervals('intersect', [[2,5], '-1',2,3,'x'], [2,3]);
    doTestIntervals('intersect', ['x',[2,-1],['5','-1e+2']], [-1,2]);
});

QUnit.test('intersect2', function() {
    try{
        PsIntervals.intersect2();
        fail();
    } catch(e) {
        ok(true, e.message)
    }
    try{
        PsIntervals.intersect2(1);
        fail();
    } catch(e) {
        ok(true, e.message)
    }
    try{
        PsIntervals.intersect2(1,2,3);
        fail();
    } catch(e) {
        ok(true, e.message)
    }
    deepEqual(PsIntervals.intersect2(undefined, undefined), null);
    
    deepEqual(PsIntervals.intersect2([[1,2], [3,4]], [2,3]), [[2,2], [3,3]]);
    deepEqual(PsIntervals.intersect2([[1,3], [4,6]], [2,5]), [[2,3], [4,5]]);
    deepEqual(PsIntervals.intersect2([[1,2], [5,6]], [3,4]), null);
    deepEqual(PsIntervals.intersect2([1,2], [3,4]), null);
    deepEqual(PsIntervals.intersect2([-1,-2], [-3,-4]), null);
    deepEqual(PsIntervals.intersect2([1,2], [1,2]), [[1,2]]);
    deepEqual(PsIntervals.intersect2([[1],[2],[3,4]], [[1,2], [2,3], [4]]), [[1,1],[2,2],[3,3],[4,4]]);
    deepEqual(PsIntervals.intersect2([['a',1,3],[-1,-2]], [[2,3], [-2,-3], [4]]), [[-2,-2],[2,3]]);
    deepEqual(PsIntervals.intersect2([['a',1,3],[-1,-2,4]], [[2,3], [-2,-3], [4]]), [[-2,-2],[2,3],[4,4]]);
    deepEqual(PsIntervals.intersect2([[-2,-3],[2,1]], [[-3,-1], 2]), [[-3,-2],[2,2]]);
    deepEqual(PsIntervals.intersect2([[-2,'-3e+1'],[3,1]], [[-10,-1], 2,3]), [[-10,-2],[2,3]]);
    deepEqual(PsIntervals.intersect2([1,2], [2,1]), [[1,2]]);
    deepEqual(PsIntervals.intersect2([1,2], [0,1]), [[1,1]]);
    deepEqual(PsIntervals.intersect2([1,2], [0,2]), [[1,2]]);
    deepEqual(PsIntervals.intersect2([-1,-2], [-2,-4]), [[-2,-2]]);
    deepEqual(PsIntervals.intersect2([-1,-2], [-1.5,-2.5]), [[-2, -1.5]]);
    deepEqual(PsIntervals.intersect2([-1,-2], [-1.5,-2.5]), [[-2, -1.5]]);
    deepEqual(PsIntervals.intersect2([[1,2], [1,2]], [[2,3], [3,2]]), [[2,2]]);
    deepEqual(PsIntervals.intersect2([[1,2], [3,4]], [[0,1.5], [3.5,2]]), [[1,1.5], [2,2], [3,3.5]]);
    deepEqual(PsIntervals.intersect2([[-1,1], [2,3]], [[-1,2.5]]), [[-1,1], [2,2.5]]);
});

QUnit.test('numTo', function() {
    
    var doTest = function(num, bounds, defaultNum, expect) {
        var actual = PsIntervals.numTo(num, bounds, defaultNum);
        deepEqual(actual, expect, 'PsIntervals.numTo('+num+', '+PsObjects.toString(bounds)+', '+defaultNum+')='+actual);
    }
    
    doTest(1, [1,2], undefined, 1);
    doTest(3, [1,2], undefined, 2);
    doTest(-1, [1,2], undefined,1);
    doTest('a', [1,2], 3, 2);
    doTest('1.5', [1,2], 3, 1.5);
    doTest(undefined, [1,2], undefined, null);
    doTest(-1, [-1,2], undefined, -1);
    doTest(0, [-1,-2], undefined, -1);
    doTest(-3, [-1,-2], undefined, -2);
    doTest(null, [-2,-5], -6, -5);
});

QUnit.test('isIn', function() {
    
    var doTestIsIn = function(expected, interval, bounds) {
        var args = PsArrays.toArray(arguments);
        if(!args.length) {
            ok(false, 'No args passed to doTestIsIn');
            return;//---
        }
        if (args[0]!==false && args[0]!==true) {
            ok(false, 'First argument of doTestIsIn should be boolean, given: ' + args[0]);
            return;//---
        }
        args.shift(); //Удалим ожидаемое значение
        if (args.length==2) {
            var actual = PsIntervals.isIn(interval, bounds);
            deepEqual(actual, expected, 'PsIntervals.isIn('+PsObjects.toString(args[0])+', '+PsObjects.toString(args[1])+')='+actual);
        } else {
            try{
                PsIntervals.isIn.apply(null, args);
                ok(false, 'Error expected')
            } catch(e) {
                ok(true, 'PsIntervals.isIn('+PsObjects.toString(args).removeFirstCharIf('[').removeLastCharIf(']')+') throws ' + e.message);
            }
        }
    }
    
    //Error
    doTestIsIn(false);
    doTestIsIn(false,1);
    doTestIsIn(false,1,2,3);
    
    //Empty
    doTestIsIn(false,1,null);
    doTestIsIn(false,null,1);
    doTestIsIn(false,undefined,undefined);
    doTestIsIn(false,undefined,undefined);
    doTestIsIn(false,NaN,undefined);
    doTestIsIn(false,undefined,1);
    
    //Nums
    doTestIsIn(true,1,1);
    doTestIsIn(false,1,2);
    doTestIsIn(false,1,[2]);
    doTestIsIn(true,3,[[1,2],3]);
    doTestIsIn(false,1,[2, 'a']);
    doTestIsIn(true,1,[2, '1']);
    doTestIsIn(true,1,[1,2]);
    doTestIsIn(true,1,[0,1]);
    doTestIsIn(true,1,[2,1]);
    doTestIsIn(true,2,[1,3]);
    doTestIsIn(false,1,[2,3]);
    doTestIsIn(true,5,[[1,2],[3,4],[5,6]]);
    doTestIsIn(false,4.5,[[1,2],[3,4],[5,6]]);
    doTestIsIn(true,5,[[1,2],[3,4],[5,6]]);
    doTestIsIn(false,7,[[1,2],[3,4],[5,6]]);
    doTestIsIn(true,-1,[0,-2]);
    doTestIsIn(false,-1,[-3,-2]);
    doTestIsIn(false,-4,[-3,-2]);
    doTestIsIn(true, 0, [1,2,[3,0]]);
    doTestIsIn(false, 0, [1,2,[3,4]]);
    doTestIsIn(true, -1.5, [[-1,-2],[3,4]]);
    doTestIsIn(false, -1.5, [[-3,-2],[3,4]]);
    
    //Intervals
    doTestIsIn(true, [1,2], [1,2]);
    doTestIsIn(true, [1,2], [0,3]);
    doTestIsIn(true, [1,2], [3,0]);
    doTestIsIn(false, [-1,2], [3,0]);
    doTestIsIn(false, [1.5,2.5], [1,2]);
    doTestIsIn(true, [1.5,2.5], [1,3]);
    doTestIsIn(true, [1.5,3,2.5], [1,3]);
    doTestIsIn(true, [0], [3,0]);
    doTestIsIn(true, [[1,2], [3,4]], [1,4]);
    doTestIsIn(false, [[1,2], [3,4]], [2,4]);
    doTestIsIn(true, [[4,3], [2,1]], [[2,0], [2.5,5]]);
    doTestIsIn(true, [-1,1], [-3,3]);
    doTestIsIn(false, [-1,1], [0,3]);
    doTestIsIn(false, [0,1], [0,-3]);
    doTestIsIn(true, [-1, 2], [-1,2]);
    doTestIsIn(true, [-1, 2], [2,-1]);
    doTestIsIn(true, [-1, -3], [[0,-2],[-2,-3]]);
    doTestIsIn(true, [1, 4], [[0,Math.PI],[Math.PI,4]]);
    doTestIsIn(true, [1, 4], [[0,Math.PI],[Math.PI,4]]);
    doTestIsIn(true, [1, 4], [[0,Math.E],[Math.E,4]]);
    doTestIsIn(false, [1, Math.PI], [[0,Math.E],[Math.E,3]]);
});

QUnit.test('line2rectangle', function() {
    deepEqual(PsIntervals.line2rectangle([-1,1], [-1,1], [1,1], [0,0], false), {
        lim: [[0,0],[1,1]],
        unlim: [[-1,-1],[1,1]]
    });
    
    deepEqual(PsIntervals.line2rectangle([-1,1], [-1,1], [1,1], [0,0]), {
        lim: [[0,0],[1,1]],
        unlim: [[-1,-1],[1,1]]
    });
    
    deepEqual(PsIntervals.line2rectangle([-1,2], [-1,1], [2,1], [0,0]), {
        lim: [[0,0],[2,1]],
        unlim: [[-1,-0.5],[2,1]]
    });
});

QUnit.test('angleToCirc', function() {
    function testAngleToCirc(grad, include, exclude, expected) {
        var ang;
        if (PsIs.defined(expected)) {
            ang = PsIntervals.angleToCirc(grad, include, exclude);
            deepEqual(ang, expected, 'PsIntervals.angleToCirc('+grad+', '+include+', '+exclude+')='+ang);
        } else {
            try {
                ang = PsIntervals.angleToCirc(grad, include, exclude);
                ok(false, 'Error is expected, ['+ang+'] is returned.');
            } catch(e) {
                ok(true, 'PsIntervals.angleToCirc('+grad+', '+include+', '+exclude+') throws '+e.message);
            }
        }
    }
    
    /* [0, 360) */
    testAngleToCirc(0, 0, 360, 0);
    testAngleToCirc(360, 0, 360, 0);
    testAngleToCirc(-360, 0, 360, 0);
    testAngleToCirc(180, 0, 360, 180);
    testAngleToCirc(-180, 0, 360, 180);
    testAngleToCirc(90, 0, 360, 90);
    testAngleToCirc(135, 0, 360, 135);
    testAngleToCirc(720, 0, 360, 0);
    testAngleToCirc(360-90, 0, 360, 360-90);
    testAngleToCirc(360+90, 0, 360, 90);
    testAngleToCirc(-90, 0, 360, 360-90);
    testAngleToCirc(-30, 0, 360, 360-30);
    testAngleToCirc(720+30, 0, 360, 30);
    testAngleToCirc(360*5, 0, 360, 0);
    
    /* (0, 360] */
    testAngleToCirc(0, 360, 0, 360);
    testAngleToCirc(360, 360, 0, 360);
    testAngleToCirc(-360, 360, 0, 360);
    testAngleToCirc(180, 360, 0, 180);
    testAngleToCirc(-180, 360, 0, 180);
    testAngleToCirc(90, 360, 0, 90);
    testAngleToCirc(135, 360, 0, 135);
    testAngleToCirc(720, 360, 0, 360);
    testAngleToCirc(360-90, 360, 0, 360-90);
    testAngleToCirc(360+90, 360, 0, 90);
    testAngleToCirc(360*5, 360, 0, 360);
    
    /* (-360, 0] */
    testAngleToCirc(0, 0, -360, 0);
    testAngleToCirc(360, 0, -360, 0);
    testAngleToCirc(-360, 0, -360, 0);
    testAngleToCirc(180, 0, -360, -180);
    testAngleToCirc(-180, 0, -360, -180);
    testAngleToCirc(90, 0, -360, -360+90);
    testAngleToCirc(135, 0, -360, -360+135);
    testAngleToCirc(720, 0, -360, 0);
    testAngleToCirc(360-90, 0, -360, -90);
    testAngleToCirc(360+90, 0, -360, -360+90);
    testAngleToCirc(720+30, 0, -360, -360+30);
    testAngleToCirc(360*5, 0, -360, 0);
    
    /* [-360, 0) */
    testAngleToCirc(0, -360, 0, -360);
    testAngleToCirc(360, -360, 0, -360);
    testAngleToCirc(-360, -360, 0, -360);
    testAngleToCirc(180, -360, 0, -180);
    testAngleToCirc(-180, -360, 0, -180);
    testAngleToCirc(90, -360, 0, -360+90);
    testAngleToCirc(135, -360, 0, -360+135);
    testAngleToCirc(720, -360, 0, -360);
    testAngleToCirc(360-90, -360, 0, -90);
    testAngleToCirc(360+90, -360, 0, -360+90);
    testAngleToCirc(720+30, -360, 0, -360+30);
    testAngleToCirc(360*5, -360, 0, -360);
    
    
    /* (-180, 180] */
    testAngleToCirc(0, 180, -180, 0);
    testAngleToCirc(360, 180, -180, 0);
    testAngleToCirc(-360, 180, -180, 0);
    testAngleToCirc(180, 180, -180, 180);
    testAngleToCirc(-180, 180, -180, 180);
    testAngleToCirc(90, 180, -180, 90);
    testAngleToCirc(135, 180, -180, 135);
    testAngleToCirc(720, 180, -180, 0);
    testAngleToCirc(360-90, 180, -180, -90);
    testAngleToCirc(360+90, 180, -180, 90);
    testAngleToCirc(-90, 180, -180, -90);
    testAngleToCirc(90, 180, -180, 90);
    testAngleToCirc(720+30, 180, -180, 30);
    testAngleToCirc(360*5, 180, -180, 0);
    
    /* [-180, 180) */
    testAngleToCirc(0, -180, 180, 0);
    testAngleToCirc(360, -180, 180, 0);
    testAngleToCirc(-360, -180, 180, 0);
    testAngleToCirc(180, -180, 180, -180);
    testAngleToCirc(-180, -180, 180, -180);
    testAngleToCirc(90, -180, 180, 90);
    testAngleToCirc(135, -180, 180, 135);
    testAngleToCirc(720, -180, 180, 0);
    testAngleToCirc(360-90, -180, 180, -90);
    testAngleToCirc(360+90, -180, 180, 90);
    testAngleToCirc(-90, -180, 180, -90);
    testAngleToCirc(90, -180, 180, 90);
    testAngleToCirc(720+30, -180, 180, 30);
    testAngleToCirc(360*5, -180, 180, 0);
    
    /* Разные */
    testAngleToCirc(135, 90, -270, -225);
    testAngleToCirc(180, 90, -270, -180);
    
    testAngleToCirc(10, 5, 6);
    testAngleToCirc(10, 0, 360.001);
});

QUnit.test('angleTo0_360', function() {
    function testAngleTo0_360(grad, expected) {
        var ang = PsIntervals.angleTo0_360(grad);
        deepEqual(ang, expected, 'PsIntervals.angleTo0_360('+grad+')='+ang);
    }
    
    /* [0, 360) */
    testAngleTo0_360(0, 0);
    testAngleTo0_360(360, 0);
    testAngleTo0_360(-360, 0);
    testAngleTo0_360(180, 180);
    testAngleTo0_360(-180, 180);
    testAngleTo0_360(90, 90);
    testAngleTo0_360(135, 135);
    testAngleTo0_360(720, 0);
    testAngleTo0_360(360-90, 360-90);
    testAngleTo0_360(360+90, 90);
    testAngleTo0_360(-90, 360-90);
    testAngleTo0_360(-30, 360-30);
    testAngleTo0_360(720+30, 30);
    testAngleTo0_360(360*5, 0);
});
