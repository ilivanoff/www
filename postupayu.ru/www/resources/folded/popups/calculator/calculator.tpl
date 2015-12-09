<h4>Калькулятор</h4>

<p>Для Вашего удобства я разработал небольшой калькулятор, который позволит выполнить практически все
    необходимые арифметические операции не покидая сайта. Вы можете использовать его в ходе выполнения
    контрольных вопросов, или же для своих нужд.
</p>

<p>Всё, что будет напечатано в поле ввода, обрабатывается как JavaScript. Если Вы с ним не знакомы - не беда, все функции можно писать в
    обычных обозначениях, принятых в математике. Просто пишите выражение, которое нужно вычислить, и жмите кнопку "рассчитать";)
</p>

<p> Вы можете использовать обычные бинарные операции: + , &#150; , * , /, скобки (), деление по модулю: %, вобщем всё как обычно.
    Для дробных чисел в качестве разделителя применяется точка.
    Вот примеры:<br />
    4+2 &nbsp; 4.3&#150;2.1 &nbsp; 4.5*2 &nbsp; 3/1.5 &nbsp; 4%2 &nbsp;
</p>

<p>Вы можете строить выражения произвольной сложности: (10%3+2*(6&#150;5.5))/2&#150;1 после вычисления даст 0.</p>

<p>Вам доступны любые функции и константы, которые есть в объекте Math языка JavaScript, а также дополнительные функции, полный 
    перечень которых приведён в таблицах ниже.
</p>

<form action="">
    <fieldset>
        <!--<legend>Введите выражение</legend>-->
        <div class="result">
            <div class="content"></div>
        </div>
        <textarea name="formula" cols="20" rows="110"></textarea>
        <p>
            <input class="button" type="submit" value="Рассчитать"/>
            <input class="button" type="reset" value="Очистить"/>
        </p>
    </fieldset>
</form>

<h2>Основные функции</h2>
<table class='colored'>
    <thead>
    <th class="col1" width="25%">Обозначение</th>
    <th class="col2" width="25%">Синтаксис JavaScript</th>
    <th>Синтаксис калькулятора</th>
</thead>

<tbody>

    <tr>
        <td>|<i>x</i>|</td>
        <td>Math.abs(<i>x</i>)</td>
        <td>abs(<i>x</i>)</td>
    </tr>

    <tr>
        <td>
            <i>x<sup>y</sup></i><br />
            <i>e<sup>x</sup></i><br />
            <i>&radic;x</i><br />
            <!--&#8730;<i>x<br />-->
            <i>x</i><sup>2</sup>
        </td>
        <td>
            Math.pow(<i>x</i>,<i>y</i>)<br />
            Math.exp(<i>x</i>)<br />
            Math.sqrt(<i>x</i>)<br />
            &nbsp;
        </td>
        <td>
            pow(<i>x</i>,<i>y</i>)<br />
            exp(<i>x</i>)<br />
            sqrt(<i>x</i>)<br />
            sq(<i>x</i>)
        </td>
    </tr>

    <tr>
        <td>ln <i>x</i></td>
        <td>Math.log(<i>x</i>)</td>
        <td>ln(<i>x</i>)
    </tr>

    <tr>
        <td>sin <i>x</i><br />cos <i>x</i><br />tg <i>x</i></td>
        <td>Math.sin(<i>x</i>)<br />Math.cos(<i>x</i>)<br />Math.tan(<i>x</i>)</td>
        <td>sin(<i>x</i>)<br />cos(<i>x</i>)<br />tg(<i>x</i>)
    </tr>

    <tr>
        <td>
            arcsin <i>x</i><br />
            arccos <i>x</i><br />
            arctg <i>x</i>
        </td>
        <td>
            Math.asin(<i>x</i>)<br />
            Math.acos(<i>x</i>)<br />
            Math.atan(<i>x</i>)
        </td>
        <td>
            arcsin(<i>x</i>)<br />
            arccos(<i>x</i>)<br />
            arctg(<i>x</i>)
    </tr>

    <tr>
        <td>
            &lfloor;<i>x</i>&rfloor;<br />
            &lceil;<i>x</i>&rceil;<br />
            &nbsp;
        </td>
        <td>
            Math.floor(<i>x</i>)<br />
            Math.ceil(<i>x</i>)<br />
            Math.round(<i>x</i>)
        </td>
        <td>
            floor(<i>x</i>)<br />
            ceil(<i>x</i>)<br />
            round(<i>x</i>,<i>n</i>)
        </td>
    </tr>

    <tr>
        <td></td>
        <td>
            Math.min(<i>a</i>,<i>b</i>)<br />
            Math.max(<i>a</i>,<i>b</i>)
        </td>
        <td>
            min([<i>a</i>,<i>b</i>,<i>c</i>,...])<br />
            max([<i>a</i>,<i>b</i>,<i>c</i>,...])
        </td>
    </tr>

    {*
    <tr>
    <td></td>
    <td>Math.random()</td>
    <td>random(<i>b</i><sub>1</sub>,<i>b</i><sub>2</sub>,<i>n</i>)</td>
    </tr>
    *}
</tbody>
</table>

<h2>Константы</h2>
<table class='colored'>
    <thead>
    <th class="col1" width="25%">Обозначение</th>
    <th class="col2" width="25%">Синтаксис JavaScript</th>
    <th>Синтаксис калькулятора</th>
</thead>

<tbody>

    <tr>
        <td>&pi;</td>
        <td>Math.PI</td>
        <td>pi</td>
    </tr>

    <tr>
        <td><i>e</i></td>
        <td>Math.E</td>
        <td>e</td>
    </tr>

</tbody>
</table>

<h2>Дополнительные функции</h2>
<table class='colored'>

    <thead>
    <th class="col1" width="25%">Синтаксис калькулятора</th>
    <th class="col2" width="25%">Входной тип данных</th>
    <th>Описание</th>
</thead>

<tbody>
    <tr>
        <td>base(<i>x</i>,<i>b</i><sub>1</sub>,<i>b</i><sub>2</sub>)</td>
        <td>Целое число</td>
        <td>конвертирует <i>x</i> из основания <i>b</i><sub>1</sub> в <i>b</i><sub>2</sub>
    </tr>

    <tr>
        <td>random(<i>a</i>,<i>b</i>,<i>n</i>)</td>
        <td>{nobr}Число a&lt;b, n &mdash; натуральное{/nobr}</td>
        <td>Возвращает случайное число от <i>a</i> до <i>b</i> с <i>n</i> знаками после запятой</td>
    </tr>

    <tr>
        <td>factor(<i>x</i>)</td>
        <td>Целое число &#62;1</td>
        <td>возвращает массив простых делителей <i>x</i></td>
    </tr>

    <tr>
        <td>
            lg <i>x</i><br/>
            log\sub{b}a
        </td>
        <td>
            Положительные числа
        </td>
        <td>
            lg(<i>x</i>)<br/>
            log(<i>a</i>,<i>b</i>)
    </tr>

    <tr>
        <td>
            factorial(<i>n</i>)<br />
            pfactorial(<i>n</i>)<br />
            ank(<i>n</i>, <i>k</i>)<br />
            cnk(<i>n</i>, <i>k</i>)
        </td>
        <td>Целое число &ge; 0</td>
        <td>
            вычисляет <i>n</i>-факториал<br />
            перемножает все простые числа &#8804; <i>n</i><br />
            вычисляет размещение A\sub{n}\sup{k}<br />
            вычисляет сочетание C\sub{n}\sup{k}
        </td>
    </tr>

    <tr>
        <td>isprime(<i>x</i>)</td>
        <td>Целое число &#62;1</td>
        <td>проверяет число на простоту</td>
    </tr>

    <tr>
        <td>
            primes(<i>x</i>)<br/>
            primepi(<i>x</i>)<br/>
            relprime(<i>x</i>)<br/>
            totient(<i>x</i>)
        </td>
        <td>
            Число
        </td>
        <td>
            возвращает массив простых чисел, меньших либо равных <i>x</i><br/>
            вычисляет кол-во простых чисел, меньших либо равных <i>x</i><br/>
            возвращает массив чисел, взаимно простых с <i>x</i><br/>
            Функция Эйлера &mdash; кол-во чисел, меньших <i>x</i> и взаимно простых с <i>x</i>
        </td>
    </tr>

<!--<tr><td>*zeta(<i>x</i>)<td>Even Integer &#62;0<td>computes the Riemann zeta function-->

    <tr>
        <td>fibonacci(<i>x</i>)</td>
        <td>Целое число &#62;0</td>
        <td>вычисляет <i>x</i>-тый элемен последовательности Фибоначчи</td>
    </tr>

    <tr>
        <td>
            gcd([<i>a</i>,<i>b</i>,<i>c</i>,...])<br />
            lcm([<i>a</i>,<i>b</i>,<i>c</i>,...])
        </td>
        <td>
            Массив чисел<br />
            Массив чисел
        </td>
        <td>
            вычисляет НОД (наибольший общий делитель) <i>a</i>,<i>b</i>,<i>c</i>,...<br />
            вычисляет НОК (наименьшее общее кратное) <i>a</i>,<i>b</i>,<i>c</i>,...
        </td>
    </tr>

    <tr>
        <td>
            product([<i>a</i>,<i>b</i>,<i>c</i>,...])<br />
            sum([<i>a</i>,<i>b</i>,<i>c</i>,...])
        </td>
        <td>
            Массив чисел<br />
            Массив чисел
        </td>
        <td>
            перемножает <i>a</i>*<i>b</i>*<i>c</i>*...<br />
            суммирует <i>a</i>+<i>b</i>+<i>c</i>+...</td>
    </tr>

    <tr>
        <td>sign(<i>x</i>)</td>
        <td>Число</td>
        <td>возвращает знак числа <i>x</i>: &#150;1, 0 или 1</td>
    </tr>

    <tr>
        <td>
            csc(<i>x</i>)<br />
            sec(<i>x</i>)<br />
            ctg(<i>x</i>)</td>
        <td>
            Число<br />
            Число<br />
            Число
        </td>
        <td>
            вычисляет косеканс <i>x</i><br />
            вычисляет секанс <i>x</i><br />
            вычисляет котангенс <i>x</i>
        </td>
    </tr>

    <tr>
        <td>
            arccsc(<i>x</i>)<br />
            arcsec(<i>x</i>)<br />
            arcctg(<i>x</i>)
        </td>
        <td>
            Число<br />
            Число<br />
            Число
        </td>
        <td>
            вычисляет обратную триг. функцию от косеканса <i>x</i><br />
            вычисляет обратную триг. функцию от секанса <i>x</i><br />
            вычисляет обратную триг. функцию от котангенса <i>x</i>
        </td>
    </tr>

    <tr>
        <td>
            sh(<i>x</i>)<br />
            ch(<i>x</i>)<br />
            th(<i>x</i>)<br />
            cth(<i>x</i>)
        </td>
        <td>
            Число<br />
            Число<br />
            Число<br />
            Число
        </td>
        <td>
            вычисляет гиперболический синус (шинус) <i>x</i><br />
            вычисляет гиперболический косинус (чёсинус) <i>x</i><br />
            вычисляет гиперболический тангенс <i>x</i><br />
            вычисляет гиперболический котангенс <i>x</i>
        </td>
    </tr>

    <tr>
        <td>
            arsh(<i>x</i>)<br />
            arch(<i>x</i>)<br />
            arth(<i>x</i>)<br />
            arсth(<i>x</i>)
        </td>
        <td>
            Число<br />
            Число<br />
            Число<br />
            Число
        </td>
        <td>
            арксинус гиперболический (ареашинус) <i>x</i><br />
            арккосинус гиперболический (ареачёсинус) <i>x</i><br />
            арктангенс гиперболический (ареатангенс) <i>x</i><br />
            арккотангенс гиперболический (ареакотангенс) <i>x</i>
        </td>
    </tr>

</tbody>

</table>


<h2>Примеры вычислений</h2>

{text}
abs(&#150;17)=17
ank(5, 3)=60
arccos(0)=1.5707963267948965
arccosh(1)=0
base(5,2)=101; base(101,2, 10)=5
ceil(pi/2)=2
cnk(5, 3)=10
exp(2)=7.38905609893065
factor(60)=[2,2,3,5]
factorial(5)=120
fibonacci(17)=1597
floor(pi/2)=1
gcd(20,25)=5
lcm(20,25)=100
log(100,10)=2
ln(2)=0.6931471805599453
max([5,65,1105])=1105
min([5,65,1105])=5
pfactorial(5)=30
primes(10)=[2,3,5,7]
primepi(10)=4
pow(2,3)=8
product([1,3,5,7,9])=945
random(1,4,2)=2.71; random(2,5,3)=3.476
relprime(10)=[1,3,7,9]
round(pi/2)=2
round(pi/2,2)=1.57
sign(3)=1
sign(&#150;3)=&#150;1
sin(pi/2)=1
sh(ln(2))=.75
sqrt(289)=17
sum([1,2,3,4])=10
totient(10)=4 ([1,3,7,9] &mdash; взаимно-просты с 10)
{/text}