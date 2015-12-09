{if $showcase_mode}

    <p>
        Всем тем, кто добросовестно прошёл предыдущие три занятия кружка, выполнил все задания и считает, что
        всё понял &mdash; предлагаю пройти данный тест.
    </p>
    <p>
        Задачи, подобные тем, из которых состоит тест, были нами рассмотрены ранее. У Вас уже должны быть все знания, которые необходимы,
        и Вы сможете добросовестно проверить свои знания.
    </p>
    <p>
        Контролировать Вас некому, но это и не нужно, так как результаты тестирования нужны, в первую очередь, именно Вам.
        По результатам теста Вы получите чёткую картину - что усвоено действительн хорошо, а над чем ещё нужно поработать.
    </p>
    <p>
        Перед началом теста даны общие рекомендации по поводу того, как проходить тест. Но я всёже прошу не начинать
        тестирование, пока Вы не изучите всё то, о чём мы говорили на трёх последних занятиях кружка, посвящённых тригонометрии.
    </p>

{else}

    <p>
        Данный тест проводится по следующим занятиям кружка:
    </p>
    <ol>
        <li>{post_href type='tr' post_ident='trigonometry_formula'}.{/post_href}</li>
        <li>{post_href type='tr' post_ident='trigonometry_krug'}.{/post_href}</li>
        <li>{post_href type='tr' post_ident='trigonometry'}.{/post_href}</li>
    </ol>
    <p>
        Если Вам действительно понятно всё то, о чём говорилось на данных занятиях, то можете
        переходить к тестированию. В противном случае рекомендую вернуться и разобрать все непонятные 
        моменты.
    </p>
    <p>
        На данный тест отводится 1 час, за который Вы должны успеть решить 20 задач.
        Задачи теста&nbsp;&mdash; типовые, и были нами рассмотреты ранее.
        Прежде, чем пожелать Вам удачи, напомню основные
    </p>

    <div class="howTo">
        <p>
            <b>Рекомендации по прохождению тестов:</b>
        </p>
        <ol>
            <li>
                <div class="head">Убедитесь, что во время теста Вас ничего не будет отвлекать</div>
                <p>
                    Я искренне прошу Вас выполнять тесты в обстановке, когда ничего извне не будет Вам мешать.
                    В противном случае результаты тестирования могут оказаться искажёнными и не очень Вас обрадуют.
                </p>
            </li>
            <li>
                <div class="head">Заранее подготовьте черновик и пару ручек</div>
                <p>
                    Я рекомендую решать каждую задачу отдельно на листе бумаги.
                    Это позволяет лучше сосредоточиться на решениине, а сам процесс решения 
                    оставит в памяти более чёткий след.
                </p>
                <p>
                    Насчёт пары ручек &mdash; это стандартная и очень правильная рекомендация.
                    Во время реального экзамена одна ручка может кончиться, и прийдётся отвлекаться, 
                    терять время и рассеивать внимание. Лучше, чтобы эти ручки были совершенно одинаковы,
                    то есть куплены одновременно в одном месте. Так вы будете уверены, что паста в ручках имеет
                    совершенно одинаковый цвет и оттнок. Экзаменационная работа, это не конспект,
                    и незачем ему пестрить разными цветами, раздражая проверяющего;)
                </p>
            </li>
            <li>
                <div class="head">Не зацикливайтесь на неподдающихся задачах</div>
                <p>
                    Если в процессе решения какие-либо задачи вызовут у Вас сложность &mdash; не паникуйте и не расстраивайтесь,
                    отложите неполучающуюся задачку на потом. 
                </p>
                <p>
                    Так Вы гарантированно не потратите лишнее время и не потеряете баллы на тех задачах, которые не будут проблемными. 
                    Вернувшись к задаче, Вы вообще можете обнаружить, что ход решения вдруг для Вас стал ясен, ведь подсознание не спит...
                    Уж поверьте мне, я так сто раз делал:)
                </p>
            </li>
        </ol>
    </div>

    <p>
        Вот теперь я Вам искренне желаю удачи! Помните &mdash; тяжело в учении, легко в бою:)
    </p>

    {psplugin name="testing" test_name="Тест по тригонометрии" time="45" id="1"}

    {*#1 задача*}
    {task}
    <p>
        Какой тригонометрической функции соответствует отношение противолежащего катета к гипотенузе
        в прямоугольном треугольнике?
    </p>
    {answers}
    {ans correct=1}sin&alpha;{/ans}
    {ans}cos&alpha;{/ans}
    {ans}tg&alpha;{/ans}
    {ans}ctg&alpha;{/ans}
    {/answers}
    {/task}


    {*#2 задача*}
    {task}
    <p>
        Какой тригонометрической функции соответствует отношение прилежащего катета к противолежащему
        в прямоугольном треугольнике?
    </p>
    {answers}
    {ans}sin&alpha;{/ans}
    {ans}cos&alpha;{/ans}
    {ans}tg&alpha;{/ans}
    {ans correct=1}ctg&alpha;{/ans}
    {/answers}
    {/task}


    {*#3 задача*}
    {task}
    <p>
        В прямоугольном треугольнике sin&alpha;=&frac12;, чему равен cos&alpha;?
    </p>
    {answers}
    {ans}&frac12;{/ans}
    {ans}&frac34;{/ans}
    {ans correct=1}\kor{3}&frasl;2{/ans}
    {ans}&frac14;{/ans}
    {/answers}
    {ans}
    <p>
        Синус и косинус острого угла прямоугольного треугольника связаны основным тригонометрическим тождеством:
        sin\sup{2}&alpha;+cos\sup{2}&alpha;=1. Подставив данные задачи, найдём ответ в одно действие:
    </p>
    \[\cos\alpha=\sqrt{1-\frac{1}{4}}=\frac{\sqrt{3}}{2}\]
    {/ans}
    {/task}


    {*#4 задача*}
    {task}
    <p>
        Может ли тангенс или котангенс острого угла прямоугольного треугольника быть больше единицы?
    </p>
    {answers}
    {ans correct=1}Да{/ans}
    {ans}Нет{/ans}
    {/answers}
    {ans}
    <p>Конечно может.</p>
    <p>tg&alpha; > 1, когда sin&alpha; > cos&alpha;.</p>
    <p>ctg&alpha; > 1, когда cos&alpha; > sin&alpha; {gray}(как в предыдущей задаче){/gray}</p>
    <p>tg&alpha; = ctg&alpha; = 1, когда cos&alpha; = sin&alpha;. Например для &alpha; = 45&deg;.</p>
    {/ans}
    {/task}


    {*#4 задача*}
    {task}
    <p>
        К какому выражению можно приветси \(tg(\frac{3\pi}{2}+\alpha)\)?
    </p>
    {answers}
    {ans}tg&alpha;{/ans}
    {ans}&minus; tg&alpha;{/ans}
    {ans}ctg&alpha;{/ans}
    {ans correct=1}&minus; ctg&alpha;{/ans}
    {/answers}
    {ans}
    <p>
        Воспользуемся формулами приведения. Угол &pi;&frasl;2 встречается нечётное число раз (три раза), значит исходная функция (тангенс)
        не мяется на ко-функцию (котангенс). Угол {nobr}3&pi;&frasl;2 + &alpha;{/nobr} принадлежит четвёртому квадранту, в котором тангенс отрицателен, 
        следовательно:
    </p>
    \[tg(\frac{3\pi}{2}+\alpha)=-ctg\alpha\]
    {/ans}
    {/task}


    {*#5 задача*}
    {task}
    <p>
        Какой формулой выражается sin\sup{2}&alpha; через ctg\sup{2}&alpha;?
    </p>
    {answers}
    {ans}sin\sup{2}&alpha; = 1 &minus; ctg\sup{2}&alpha;{/ans}
    {ans}sin\sup{2}&alpha; = 1 + ctg\sup{2}&alpha;{/ans}
    {ans}sin\sup{2}&alpha; = 1 &frasl; (1 &minus; ctg\sup{2}&alpha;){/ans}
    {ans correct=1}sin\sup{2}&alpha; = 1 &frasl; (1 + ctg\sup{2}&alpha;){/ans}
    {/answers}
    {ans}
    {varres}
    \[ctg^2\alpha=\frac{\cos^2\alpha}{\sin^2\alpha}=\frac{1-\sin^2\alpha}{\sin^2\alpha}=\frac{1}{\sin^2\alpha}-1\]
    <p>
        Отсюда следует ответ:
    </p>
    \[\sin^2\alpha=\frac{1}{1+ctg^2\alpha}\]

    {varres}
    <p>
        Второй вариант решения - логический. Во всех вариантах ответа слева стоит sin\sup{2}&alpha;, но поскольку
        величина в квадрате всегда положительна, а синус не может быть больше единицы, имеем:
    </p>

    {f}0 &le; sin\sup{2}&alpha; &le; 1{/f}

    <p>
        А далее отбрасываем все варианты по порядку:
    </p>

    <ol>
        <li>1 &minus; ctg\sup{2}&alpha; - может быть меньше ноля</li>
        <li>1 + ctg\sup{2}&alpha; - может быть больше единицы</li>
        <li>1 &frasl; (1 &minus; ctg\sup{2}&alpha;) - может быть меньше ноля или вообще может возникнуть деление на 0</li>
        <li>1 &frasl; (1 + ctg\sup{2}&alpha;) - подхидот по всем параметрам, т.к. всегда больше ноля и всегда меньше либо равен единицы</li>
    </ol>

    {/ans}
    {/task}


    {*#6 задача*}
    {task}
    <p>
        Радиус окружности равен 10см. Чему равна длина дуги, ограниченной центральным углом в 2 радиана?
    </p>
    {answers}
    {ans}5 см.{/ans}
    {ans}10 см.{/ans}
    {ans correct=1}20 см.{/ans}
    {ans}30 см.{/ans}
    {/answers}

    {ans}

    <p>
        Длина дуги связана с радианной мерой угла соотношением:
    </p>

    {f}&#301; = &alpha; r = 2 &sdot; 10 = 20 [см]{/f}

    {/ans}
    {/task}


    {*#7 задача*}
    {task}
    <p>
        sin(2011&pi;) равен?
    </p>
    {answers}
    {ans correct=1}0{/ans}
    {ans}&frac12;{/ans}
    {ans}1{/ans}
    {ans}-1{/ans}
    {/answers}

    {ans}

    <p>
        Синус &mdash; периодическая функция с периодом 2&pi;. С учётом этого можем записать:
    </p>

    {f}sin(2011&pi;) = sin(2010&pi; + &pi;) = sin(1005&sdot;2&pi; + &pi;) = sin(&pi;) = 0{/f}

    {/ans}
    {/task}


    {*#8 задача*}
    {task}
    <p>
        Какому углу в градусах соответствует угол \(\frac{4\pi}{5}\) радиан?
    </p>
    {answers}
    {ans}85&deg;{/ans}
    {ans}120&deg;{/ans}
    {ans correct=1}144&deg;{/ans}
    {ans}160&deg;{/ans}
    {/answers}
    {ans}
    <p>
        Радианная и градусная меры угла связаны соотношением:
        \[\alpha = \frac{\pi}{180^{\circ}}n\]
        где n - угол в градусах.
    </p>

    <p>
        Выразив из этой формулы n и подставив условия задачи, получим:
    </p>

    \[n = \frac{180^{\circ} \alpha}{\pi}=\frac{180^{\circ}\cdot4\pi}{5\pi} = 144^{\circ}\]

    {/ans}
    {/task}


    {*#9 задача*}
    {task}
    <p>
        Чему равен \(\cos\frac{4\pi}{3}\) ?
    </p>
    {answers}
    {ans}&frac12;{/ans}
    {ans}&frac14;{/ans}
    {ans correct=1}&minus; &frac12;{/ans}
    {ans}&minus; &frac14;{/ans}
    {/answers}
    {ans}

    <p>
        \(\cos\frac{4\pi}{3} = \cos\Bigl(\pi + \frac{\pi}{3}\Bigr)\). Для этого выражения
        удобно воспользоваться формулой приведения. Поскольку &pi; = 2&sdot;&pi;&frasl;2, т.е. &pi;&frasl;2 
        стоит чётное число раз, значит косинус на синус не меняется. Угол 4&pi;&frasl;3 лежит в третьем квадранте,
        где косинус отрицателен. Окончательно получим ответ:
    </p>

    \[\cos\frac{4\pi}{3} = \cos\Bigl(\pi + \frac{\pi}{3}\Bigr) = - \cos\frac{\pi}{3} = - \frac{1}{2}\]

    {/ans}
    {/task}


    {*#10 задача*}
    {task}
    <p>
        Чему равен \(\sin\Bigl(-\frac{8\pi}{3}\Bigr)\) ?
    </p>
    {answers}
    {ans}&frac12;{/ans}
    {ans}\(\frac{\sqrt{3}}{2}\){/ans}
    {ans}&minus; &frac12;{/ans}
    {ans correct=1}\(-\frac{\sqrt{3}}{2}\){/ans}
    {/answers}
    {ans}

    \[
    \begin{aligned}
    \sin\Bigl(-\frac{8\pi}{3}\Bigr)&=\sin\Bigl(-\frac{9\pi}{3}+\frac{\pi}{3}\Bigr)=
    \sin\Bigl(-3\pi+\frac{\pi}{3}\Bigr)=\sin\Bigl(4\pi+(-3\pi)+\frac{\pi}{3}\Bigr)=\\
    &=\sin\Bigl(\pi+\frac{\pi}{3}\Bigr)=-\sin\frac{\pi}{3}=-\frac{\sqrt{3}}{2}
    \end{aligned}
    \]

    <p>
        В процессе преобразований сначала была использована периодичностью синуса,
        затем применена формула приведения.
    </p>

    {/ans}
    {/task}


    {*#11 задача*}
    {task}
    <p>
        Как выражается ctg&alpha; через \(tg\frac{\alpha}{2}\)?
    </p>
    {answers}
    {ans}ctg&alpha; = \(\frac{tg\frac{\alpha}{2}}{2}\){/ans}
    {ans correct=1}ctg&alpha; = \(\frac{1-tg^2\frac{\alpha}{2}}{2tg\frac{\alpha}{2}}\){/ans}
    {ans}ctg&alpha; = \(\frac{2}{tg\frac{\alpha}{2}}\){/ans}
    {ans}ctg&alpha; = \(\frac{1+tg^2\frac{\alpha}{2}}{2tg\frac{\alpha}{2}}\){/ans}
    {/answers}
    {ans}

    \[ctg\alpha=\frac{\cos\alpha}{\sin\alpha}=\frac{\cos^2\frac{\alpha}{2}-\sin^2\frac{\alpha}{2}}{2\sin\frac{\alpha}{2}\cos\frac{\alpha}{2}}=\frac{1-tg^2\frac{\alpha}{2}}{2tg\frac{\alpha}{2}}\]

    <p>
        В процессе перобразований мы воспользовались формулами двойного аргумента, а в последнем преобразовании разделили числитель и знаменатель 
        на \(\cos^2\frac{\alpha}{2}\).
    </p>

    {/ans}
    {/task}

    {*#12 задача*}
    {task}
    <p>
        К какому выражению можно привести выражение (cos&alpha; &minus; cos&beta;)\sup{2} + (sin&alpha; &minus; sin&beta;)\sup{2}?
    </p>
    {answers}
    {ans}\(2\sin\frac{\alpha-\beta}{2}\){/ans}
    {ans correct=1}\(4\sin^2\frac{\alpha-\beta}{2}\){/ans}
    {ans}2sin(&alpha;+&beta;){/ans}
    {ans}&minus; 4cos\sup{2}(&alpha;&minus;&beta;){/ans}
    {/answers}
    {ans}

    <p>
        {nobr}(cos&alpha; &minus; cos&beta;)\sup{2} + (sin&alpha; &minus; sin&beta;)\sup{2} = 
        cos\sup{2}&alpha; &minus; 2cos&alpha;cos&beta; + cos\sup{2}&beta; + sin\sup{2}&alpha; &minus; 2sin&alpha;sin&beta; + sin\sup{2}&beta; ={/nobr}
        {nobr}= 2 &minus; 2(cos&alpha;cos&beta; + sin&alpha;sin&beta;) = 2(1 &minus; cos(&alpha; &minus; &beta;)){/nobr}
    </p>

    <p>
        Далее обозначим &gamma;=(&alpha;&minus;&beta;)&frasl;2 и воспользуемся формулой косинуса двойного аргумента: 
    </p>

    <p>
        2(1 &minus; cos2&gamma;) = 2(1 &minus; (1 &minus; 2sin\sup{2}&gamma;)) = 4sin\sup{2}&gamma;.
    </p>

    <p>
        Выплнив обратную замену, окончательно найдём:
    </p>

    \[(\cos\alpha - \cos\beta)^2 + (\sin\alpha - \sin\beta)^2 = 4\sin^2\frac{\alpha-\beta}{2}\]

    <p>
        Подумайте, как можно вообще не решая задачу выбрать правильный ответ?
    </p>

    <p>
        {hidden}
        В условии задачи стоит сумма квадратов, которая всегда положительна. Таким образом после преобразований также должно получиться
        выражение, которое всегда положительно, вне зависимости от конкретных значений углов &alpha; и &beta;. Внимательно посмотрев на предложенные
        варианты, легко можно выделить правильный ответ.
        {/hidden}
    </p>

    {/ans}
    {/task}


    {task}
    <p>
        Преобразовав произведение в сумму, вычислите значение выражения sin15&deg;cos45&deg;.
    </p>

    {answers}
    {ans}&frac12;{/ans}
    {ans correct=1}\(\frac{\sqrt{3}-1}{4}\){/ans}
    {ans}\kor{3}{/ans}
    {ans}\(\frac{\sqrt{3}+1}{4}\){/ans}
    {/answers}

    {ans}
    <p>
        По формуле преобразования произведения синуса на косинус в сумму, получим:
    </p>

    \[
    \begin{aligned}
    \sin15^{\circ}\cos45^{\circ}&=\frac{\sin(15^{\circ} + 45^{\circ}) + \sin(15^{\circ}-45^{\circ})}{2}
    = \frac{\sin60^{\circ} - \sin30^{\circ}}{2}\\
    &=\frac{\frac{\sqrt{3}}{2}-\frac{1}{2}}{2}=\frac{\sqrt{3}-1}{4}
    \end{aligned}
    \]
    {/ans}
    {/task}


    {*#13 задача*}
    {task}
    <p>
        Найти значение выражения \(\sin^{2}2\alpha-\cos(\frac{\pi}{3}-2\alpha)\sin(2\alpha-\frac{\pi}{6})\).
    </p>
    {answers}
    {ans}1&frasl;2{/ans}
    {ans}2&frasl;5{/ans}
    {ans correct=1}1&frasl;4{/ans}
    {ans}3&frasl;4{/ans}
    {/answers}
    {ans}

    <p>
        Воспользовавшись чётностью косинуса, а также формулами синуса и косинуса разности, получим:
    </p>
    \[
    \begin{aligned}
    \cos(\frac{\pi}{3}-2\alpha)&=\cos(2\alpha-\frac{\pi}{3})=\cos2\alpha\cos\frac{\pi}{3}+\sin2\alpha\sin\frac{\pi}{3}=\\
    &=\frac{1}{2}\cos2\alpha+\frac{\sqrt{3}}{2}\sin2\alpha \\

    \sin(2\alpha-\frac{\pi}{6})&=\sin2\alpha\cos\frac{\pi}{6} - \cos2\alpha\sin\frac{\pi}{6}=\\
    &=\frac{\sqrt{3}}{2}\sin2\alpha-\frac{1}{2}\cos2\alpha
    \end{aligned}
    \]

    <p>
        Перемножив полученные выражения и свернув произведение по формуле разности квадратов, получим:
    </p>

    \[
    (\frac{\sqrt{3}}{2}\sin2\alpha+\frac{1}{2}\cos2\alpha)(\frac{\sqrt{3}}{2}\sin2\alpha-\frac{1}{2}\cos2\alpha)=
    \frac{3}{4}\sin^{2}2\alpha - \frac{1}{4}\cos^{2}2\alpha = \\
    =\frac{3}{4}\sin^{2}2\alpha-\frac{1}{4}(1-\sin^{2}2\alpha) = \sin^{2}2\alpha-\frac{1}{4}
    \]

    <p>
        Подставив полученное выражение в исходное уравнение, найдём:
    </p>

    \[
    \sin^{2}2\alpha-\cos(\frac{\pi}{3}-2\alpha)\sin(2\alpha-\frac{\pi}{6})=\sin^{2}2\alpha-(\sin^{2}2\alpha-\frac{1}{4})=\frac{1}{4}
    \]
    {/ans}
    {/task}


    {*#14 задача*}
    {task}
    <p>
        Найти значение выражения \(\frac{1}{\sin10^{\circ}}-\frac{\sqrt{3}}{\cos{10^\circ}}\).
    </p>
    {answers}
    {ans}&minus; 1{/ans}
    {ans}\kor{3}{/ans}
    {ans}2{/ans}
    {ans correct=1}4{/ans}
    {/answers}
    {ans}

    \[
    \begin{aligned}
    \frac{1}{\sin10^{\circ}}-\frac{\sqrt{3}}{\cos{10^\circ}}&=
    \frac{\cos{10^\circ}-\sqrt{3}\sin10^{\circ}}{\sin10^{\circ}\cos{10^\circ}}=
    \frac{2(\frac{1}{2}\cos{10^\circ}-\frac{\sqrt{3}}{2}\sin10^{\circ})}{\frac{1}{2}\sin20^{\circ}}\\
    &=4~\frac{\frac{1}{2}\cos{10^\circ}-\frac{\sqrt{3}}{2}\sin10^{\circ}}{\sin20^{\circ}} = A
    \end{aligned}
    \]

    <p>
        Учитывая, что:
    </p>

    \[
    \begin{aligned}
    \frac{1}{2}&=\sin30^{\circ}\\
    \frac{\sqrt{3}}{2}&=\cos30^{\circ}
    \end{aligned}
    \]

    <p>
        Сделаем замену и воспользуемся формулой синуса разности:
    </p>

    \[
    A = 4~\frac{\sin30^{\circ}\cos{10^\circ}-\cos30^{\circ}\sin10^{\circ}}{\sin20^{\circ}}=4~\frac{\sin(30^{\circ}-10^{\circ})}{\sin20^{\circ}}
    =4~\frac{\sin20^{\circ}}{\sin20^{\circ}}=4
    \]

    {/ans}
    {/task}


    {*#15 задача*}
    {task}
    <p>
        Найти значение выражения \(tg\frac{\pi}{9}+4\sin\frac{\pi}{9}\).
    </p>
    {answers}
    {ans}1{/ans}
    {ans}2{/ans}
    {ans correct=1}\kor{3}{/ans}
    {ans}2\kor{3}{/ans}
    {/answers}
    {ans}

    \[
    \begin{aligned}
    tg\frac{\pi}{9}+4\sin\frac{\pi}{9}&=\frac{\sin\frac{\pi}{9}+4\sin\frac{\pi}{9}\cos\frac{\pi}{9}}{\cos\frac{\pi}{9}}=
    \frac{\sin\frac{\pi}{9}+2\sin\frac{2\pi}{9}}{\cos\frac{\pi}{9}}=
    \frac{(\sin\frac{\pi}{9}+\sin\frac{2\pi}{9})+\sin\frac{2\pi}{9}}{\cos\frac{\pi}{9}}=\\
    &=\frac{(2\sin\frac{\pi}{6}\cos\frac{\pi}{18})+\sin\frac{2\pi}{9}}{\cos\frac{\pi}{9}}=
    \frac{\cos\frac{\pi}{18}+\sin\frac{2\pi}{9}}{\cos\frac{\pi}{9}}=
    \frac{\sin(\frac{\pi}{2}-\frac{\pi}{18})+\sin\frac{2\pi}{9}}{\cos\frac{\pi}{9}}=\\
    &=\frac{\sin\frac{4\pi}{9}+\sin\frac{2\pi}{9}}{\cos\frac{\pi}{9}}=
    \frac{2\sin\frac{\pi}{6}\cos\frac{\pi}{9}}{\cos\frac{\pi}{9}}=\sqrt{3}
    \end{aligned}
    \]

    <p>
        В процессе преобразований мы несколько раз воспользовались формулой сложения синусов.
    </p>
    {/ans}
    {/task}


    {task}
    <p>
        Какое выражение соответствует sin15&deg;?
    </p>

    {answers}
    {ans}1&frasl;5{/ans}
    {ans correct=1}\(\frac{\sqrt{6}-\sqrt{2}}{4}\) или \(\frac{\sqrt{2-\sqrt{3}}}{2}\){/ans}
    {ans}3&frasl;5{/ans}
    {ans}\(\frac{\sqrt{6}+\sqrt{2}}{4}\) или \(\frac{\sqrt{2+\sqrt{3}}}{2}\){/ans}
    {/answers}

    {ans}
    {*START*}
    {varres}
    <p>
        Используем формулу косинуса двойного аргумента: 
    </p>
    {f}cos2&alpha; = 1 &minus; 2sin\sup{2}&alpha;{/f}
    <p>
        Приняв &alpha;=15&deg;, найдём: cos30&deg; = \kor{3}&frasl;2 = 1 &minus; 2sin\sup{2}15&deg;, откуда:
        sin\sup{2}15&deg; = (1 &minus; \kor{3}&frasl;2)&frasl;2.
    </p>
    <p>
        Окончательно:
    </p>

    \[\sin15^{\circ}=\frac{\sqrt{2-\sqrt{3}}}{2}\]

    {varres}
    <p>
        Более изящный вариант решения получается, если догадаться, что 15&deg; = 45&deg; &minus; 30&deg;. 
        Тогда можно в одно действие записать:
    </p>

    {f}sin(45&deg; &minus; 30&deg;) = sin45&deg;cos30&deg; &minus; cos45&deg;sin30&deg;{/f}

    <p>
        Учтя, что sin45&deg; = cos45&deg; = \kor{2}&frasl;2, cos30&deg; = \kor{3}&frasl;2, sin30&deg; = &frac12;,
        получим ответ в более изящной форме:
    </p>

    \[\sin15^{\circ}=\frac{\sqrt{6}-\sqrt{2}}{4}\]

    {*END*}
    {/ans}
    {/task}


    {task}
    <p>
        Найти значение выражения \(\frac{2}{3+4\cos2\alpha}\), если tg&alpha; = 0,2?
    </p>
    {answers}
    {ans}0,5{/ans}
    {ans correct=1}26&frasl;87{/ans}
    {ans}1{/ans}
    {ans}32&frasl;95{/ans}
    {/answers}

    {ans}
    <p>
        Задача довольно просто решается, если вспомнить, что синус и косинус любого угла можно представить через тангенс
        половины этого угла. Напомню вывод на промере cos2&alpha;:
    </p>
    \[\cos2\alpha=\frac{\cos2\alpha}{1}=\frac{\cos^2\alpha-\sin^2\alpha}{\cos^2\alpha+\sin^2\alpha}=\frac{1-tg^2\alpha}{1+tg^2\alpha}\]
    <p>
        Подставив параметры задачи, найдём: cos2&alpha; = (1 &minus; 0,04)&frasl;(1 + 0,04) = 12&frasl;13. С учётом этого найдём ответ: 26&frasl;87.
    </p>
    {/ans}
    {/task}


    {task}
    <p>
        Найти значение выражения \(ctg\frac{13\pi}{12} - ctg\frac{5\pi}{12}\)
    </p>

    {answers}
    {ans}&frac12;{/ans}
    {ans}1{/ans}
    {ans}\kor{3}{/ans}
    {ans correct=1}2\kor{3}{/ans}
    {/answers}

    {ans}
    \[
    ctg\frac{13\pi}{12} - ctg\frac{5\pi}{12}&=\frac{\cos\frac{13\pi}{12}}{\sin\frac{13\pi}{12}}-\frac{\cos\frac{5\pi}{12}}{\sin\frac{5\pi}{12}}=
    \frac{\sin\frac{5\pi}{12}\cos\frac{13\pi}{12}-\cos\frac{5\pi}{12}\sin\frac{13\pi}{12}}{\sin\frac{13\pi}{12}\sin\frac{5\pi}{12}}=\frac{A}{B}
    \]

    <p>
        В числителе применим формулу синуса суммы:
    </p>

    \[
    A&=\sin(\frac{5\pi}{12}-\frac{13\pi}{12})=\sin(-\frac{8\pi}{12})=-\sin\frac{2\pi}{3}
    \]
    {*=-\sin(\pi-\frac{\pi}{3})=-\sin\frac{\pi}{3}=-\frac{\sqrt{3}}{2}*}
    <p>
        В знаменателе применим формулу преобразования произведения синусов в сумму:
    </p>

    \[
    \begin{aligned}
    B&=\frac{\cos(\frac{13\pi}{12}-\frac{5\pi}{12})-\cos(\frac{13\pi}{12}+\frac{5\pi}{12})}{2}=\frac{\cos\frac{8\pi}{12}-\cos\frac{18\pi}{12}}{2}=
    \frac{\cos\frac{2\pi}{3}-\cos\frac{3\pi}{2}}{2}=\\
    &=\frac{\cos\frac{2\pi}{3}}{2}
    \end{aligned}
    \]

    <p>
        Поделив полученные представления числителя и знаменятеля, получим:
    </p>

    \[
    \frac{A}{B}=-2~\frac{\sin\frac{2\pi}{3}}{\cos\frac{2\pi}{3}}
    \]

    <p>
        Далее имеется, как минимум, три варианта, чтобы вычислить это выражение. Приведу их все:
    </p>

    <ol>
        <li>
            <b>Использовать формулы приведения:</b>
            \[
            \begin{aligned}
            \sin\frac{2\pi}{3}&=\sin(\pi-\frac{\pi}{3})=\sin\frac{\pi}{3}=\frac{\sqrt{3}}{2}\\
            \cos\frac{2\pi}{3}&=\cos(\pi-\frac{\pi}{3})=-\cos\frac{\pi}{3}=-\frac{1}{2}\\
            \end{aligned}
            \]
        </li>
        <li>
            <b>Использовать формулы двойного угла:</b>
            \[
            \begin{aligned}
            \sin\frac{2\pi}{3}&=2\sin\frac{\pi}{3}\cos\frac{\pi}{3}=2~\frac{\sqrt{3}}{2}~\frac{1}{2}=\frac{\sqrt{3}}{2}\\
            \cos\frac{2\pi}{3}&=\cos^2\frac{\pi}{3}-\sin^2\frac{\pi}{3}=\frac{1}{4}-\frac{3}{4}=-\frac{1}{2}\\
            \end{aligned}
            \]
        </li>
        <li>
            <b>Использовать формулу для тангенса двойного угла:</b>
            \[
            \frac{\sin\frac{2\pi}{3}}{\cos\frac{2\pi}{3}}=tg\frac{2\pi}{3}=\frac{2tg\frac{\pi}{3}}{1-tg^2\frac{\pi}{3}}=\frac{2\sqrt{3}}{1-3}=-\sqrt{3}
            \]

            {notice}Здесь учтено, что tg&pi;&frasl;3 = \kor{3}.{/notice}
        </li>
    </ol>

    <p>
        Вне зависимости от способа решения, получим один и тот-же ответ:
    </p>

    \[\frac{A}{B}&=(-2)(-\sqrt{3})=2\sqrt{3}\]

    {/ans}
    {/task}

    {/psplugin}

{/if}
