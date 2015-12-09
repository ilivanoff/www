{if $showcase_mode}

    <p>Какой самый лучший способ вспомнить тригонометрическую юормулу? Ну конечно же - вывести её;)</p>
    <p>Так уж получается, что тригонометрия, являясь далеко не самым сложным разделом математики,
        постоянно является предметом нелюбви со стороны школьников и студентов.</p>
    <p>Особенно учитывая кол-во формул, которые нужно помнить и иметь ввиду... А учитывая, что в
        стрессовой ситуации (на экзамене, к примеру;) вероятность вспомнить необходимое выражение
        вообще резко падает, так и тем более тригонометрия начинает вызывать "заслуженную" нелюбовь.
    </p>
    <p>В данном уроке мы поговорим о тригономерии, начиная с самых азов - что за синусы и косинусы такие,
        кому и для чего нужны, как с ними работать и не путаться в них.
    </p>

{else}

    <p>
        Теперь мы уже знаем практически всё, что нужно знать о тригонометрических функциях.
        Нами получены основные тригонометричесикие соотношния, вывдены необходимые формулы
        и изучены свойства основных трогонометрических функций - синуса, косинуса и тангенса.
    </p>

    <p>
        Теперь, я надеюсь, вы будете гораздо смелее работать с тригонометричскими функциями,
        оперировать ими и никогда в них не запутаетесь.
    </p>

    <p>
        Сегодня мы займёмся непосредственно выводом всех остальных формул тригонометрии, которые
        могут только пригодится. Вы увидите, что все они получаются на основе очень маленького набора
        формул, которые нам уже известны.
    </p>

    <p>
        Сразу скажу, что все формулы мы будем получать для синуса, косинуса и тангенса.
        Секанс, косеканс и котангенс рассматриваьт не будем вообще, т.к. все они - "перевёрнутые"
        косинус, синус и тангенс соответственно:) Причём секанс и косеканс вообще сейчас
        практически не ипользуются.
    </p>

    <p>
        Итак, поехали.
    </p>

    {partition}PLAN{/partition}
    {ANONS}

    {partition}VIDEO{/partition}
    {video dir='trainings' name='prepare(H.264)'}

    {partition}CONSPECT{/partition}

    {part}Повторение{/part}
    <p>
        Для начала давайте вспомним то, что нам уже известно. 
    </p>

    <p>
        Тангенс есть отношение синуса к косинусу, котангенс - отношение косинуса к синусу:
    </p>

    {f num='1.1'}
    \[
    \begin{aligned}
    tg\alpha&={\sin\alpha\over\cos\alpha}\\
    ctg\alpha&={1\over tg\alpha}={\cos\alpha\over\sin\alpha}\\
    \end{aligned}
    \]
    {/f}

    <p>
        Из определение тангенса и котангенса очевидно, что:
    </p>

    {f}tg&alpha;&sdot;ctg&alpha;=1{/f}

    <p>
        Косинус - чётная функция. Синус, тангенс и котангенс - нечётные функции:
    </p>

    {f num=1.2}
    \[
    \begin{aligned}
    \sin(-\alpha)&=-\sin\alpha, \\
    \cos(-\alpha)&=\cos\alpha, \\
    tg(-\alpha)&=-tg\alpha \\
    \end{aligned}
    \]
    {/f}

    <p>
        Синус и косинус связаны основным тригонометрическим тождеством:
    </p>

    {f num='1.3'}\[\sin^{2}\alpha + \cos^{2}\alpha = 1\]{/f}

    <p>
        Поделив {'1.3'|fhref} сначала на квадрат косинуса, затем на квадрат синуса, с
        учётом определений тангенса и котангенса {'1.1'|fhref}, получим:
    </p>

    {f num='1.4'}
    \[
    \begin{aligned}
    1 + tg^2\alpha&={1\over\cos^2\alpha}\\
    1 + ctg^2\alpha&={1\over\sin^2\alpha}\\
    \end{aligned}
    \]{/f}

    <p>
        Также на прошлом занятии нами получены формулы сложения, которые сегодня мы будем активно использовать:
    </p>

    {f num='1.5'}
    \[
    \begin{aligned}
    \sin(\alpha\pm\beta)&=\sin\alpha\cos\beta\pm\cos\alpha\sin\beta\\
    \cos(\alpha\pm\beta)&=\cos\alpha\cos\beta\mp\sin\alpha\sin\beta
    \end{aligned}
    \]
    {/f}

    <p>
        Выведем формулу сложения для тангенса:
    </p>

    \[
    tg(\alpha\pm\beta)={\sin(\alpha\pm\beta)\over\cos(\alpha\pm\beta)}=
    {\sin\alpha\cos\beta\pm\cos\alpha\sin\beta\over{\cos\alpha\cos\beta\mp\sin\alpha\sin\beta}}
    \]

    <p>
        Поделив числитель и знаменатель последней дроби на cos&alpha;cos&beta;, получим:
    </p>

    {f num='1.6'}\[tg(\alpha\pm\beta)={tg\alpha\pm tg\beta\over {1\mp tg\alpha tg\beta}}\]{/f}


    {part}Формулы двойного аргумента{/part}


    <p>
        Из формул сложения {1.5|fhref}, взяв слева плюс и положив &beta;=&alpha;, получим:
    </p>

    {f num='2.1'}\[\sin2\alpha=2\sin\alpha\cos\alpha\]{/f}
    {f num='2.2'}\[\cos2\alpha=\cos^{2}\alpha-\sin^2\alpha=2\cos^{2}\alpha-1=1-2\sin^{2}\alpha\]{/f}

    <p>
        В последнем случае мы воспользовались основным тригонометрическим тождеством {1.3|fhref}.
    </p>

    <p>
        Полученные нами формулы называются формулами двойного аргумента. Они часто используются и поэтому
        лучше их сразу запомнить. С помощью них, например, можно "на ровном месте" разложить синус или косинус:
    </p>

    {f num = '2.1.1'}\[\sin \alpha = \sin 2{\alpha\over 2} = 2\sin{\alpha\over 2}\cos{\alpha\over 2}\]{/f}
    {f num = '2.1.2'}\[\cos\alpha = \cos 2{\alpha\over 2} = \cos^2{\alpha\over 2}-\sin^2{\alpha\over 2}=2\cos^2{\alpha\over 2}-1=1-2\sin^2{\alpha\over 2}\]{/f}

    <p>
        Этот приём нами ещё будет использован в дальнейшем.
    </p>

    <p>
        Для тангенса из {1.6|fhref} следует:
    </p>

    {f num='2.3'}\[tg 2\alpha ={2 tg\alpha\over{1-tg^2\alpha}}\]{/f}


    {part}Формулы понижения степени{/part}


    <p>
        Формула двойного аргумента для косинуса {2.2|fhref} позволяет легко выражать квадрат
        синуса или косинуса через косинус двойного угла, но стоящего уже без квадрата, т.е.
        понизить степень:
    </p>

    {f num='3.1'}\[\sin^2\alpha = {1-\cos 2\alpha\over 2}\]{/f}
    {f num='3.2'}\[\cos^2\alpha = {1+\cos 2\alpha\over 2}\]{/f}

    <p>Поделив {'3.1'|fhref} на {'3.2'|fhref}, найдём:</p>

    {f num='3.3'}\[tg^2\alpha = {\sin^2\alpha\over\cos^2\alpha} = {1-\cos 2\alpha\over{1+\cos 2\alpha}}\]{/f}


    {part}Формулы половинного аргумента{/part}

    <p>
        Из формул понижения степени {'3.1'|fhref}, {'3.2'|fhref} и {'3.3'|fhref} заменой &alpha; на \(\alpha\over 2\) следют формулы
        половинного аргумента:
    </p>

    {f num='4.1'}\[\sin^2{\alpha\over 2} = {1-\cos \alpha\over 2}\]{/f}
    {f num='4.2'}\[\cos^2{\alpha\over 2} = {1+\cos \alpha\over 2}\]{/f}
    {f num='4.3'}\[tg^2  {\alpha\over 2} = {1-\cos \alpha\over{1+\cos \alpha}}\]{/f}


    <p>
        Правда для тангенса половинного аргумента есть куда более удобная формула, получим её:
    </p>

    <p>\(tg{\alpha\over 2}={\sin{\alpha\over 2}\over\cos{\alpha\over 2}}=
        {\sin{\alpha\over 2}\over\cos{\alpha\over 2}}\times{2\cos{\alpha\over 2}\over 2\cos{\alpha\over 2}}=
        {{2\sin{\alpha\over 2}\cos{\alpha\over 2}}\over{2\cos^2{\alpha\over 2}}}={\sin\alpha\over{1+\cos\alpha}}\)</p>

    <p>
        В последнем преобразовании для числителя мы воспользовались формулой синуса двойного угла {'2.1.1'|fhref},
        знаменатель выразили через косинус двойного угла {'2.1.2'|fhref}.
    </p>

    <p>
        Совершенно аналогично домножив в этот раз числитель и знаменатель на синус половинного угла, получим:
    </p>

    <p>\(tg{\alpha\over 2}={\sin{\alpha\over 2}\over\cos{\alpha\over 2}}=
        {\sin{\alpha\over 2}\over\cos{\alpha\over 2}}\times{2\sin{\alpha\over 2}\over 2\sin{\alpha\over 2}}=
        {{2\sin^2{\alpha\over 2}}\over{2\sin{\alpha\over 2}\cos{\alpha\over 2}}}=
        {1-\cos\alpha\over\sin\alpha}\)</p>

    <p>Окончатльно:</p>

    {f num='4.4'}\[
    tg{\alpha\over 2}={\sin\alpha\over{1+\cos\alpha}}={1-\cos\alpha\over\sin\alpha}
    \]{/f}

    <p>
        Как видим, в данной формуле тангенс стоит без квадрата и нам уже не нужно думать о знаке.
    </p>


    {part}Формулы преобразования произведения в сумму{/part}

    <p>
        В формулах сложения для синуса {'1.5'|fhref} взяв слева плюс и минус, получим систему:
    </p>

    \[\left \lbrace
    \begin{aligned}
    \sin(\alpha+\beta)&=\sin\alpha\cos\beta+\cos\alpha\sin\beta \\
    \sin(\alpha-\beta)&=\sin\alpha\cos\beta-\cos\alpha\sin\beta
    \end{aligned}\right.\]

    <p>
        Складывая верхнее уравнение с нижним, найдём:
    </p>

    {f num=5.1}\[
    \sin\alpha\cos\beta={\sin(\alpha+\beta)+\sin(\alpha-\beta)\over 2}
    \]{/f}

    <p>
        В формулах сложения для косинуса {1.5|fhref} взяв слева плюс и минус, получим систему:
    </p>

    \[\left \lbrace
    \begin{aligned}
    \cos(\alpha+\beta)&=\cos\alpha\cos\beta-\sin\alpha\sin\beta \\
    \cos(\alpha-\beta)&=\cos\alpha\cos\beta+\sin\alpha\sin\beta
    \end{aligned}\right.
    \]

    <p>
        Складывая верхнее уравнение с нижним, найдём:
    </p>

    {f num='5.2'}\[
    \cos\alpha\cos\beta={\cos(\alpha+\beta)+\cos(\alpha-\beta)\over 2}
    \]{/f}

    <p>
        Вычитая из нижнего уравнение верхнее, найдём:
    </p>

    {f num='5.3'}\[
    \sin\alpha\sin\beta={\cos(\alpha-\beta)-\cos(\alpha+\beta)\over 2}
    \]{/f}

    <p>
        Для произведения тангенсов:
    </p>

    {f num='5.4'}\[
    tg\alpha tg\beta={\sin\alpha\sin\beta\over\cos\alpha\cos\beta}=
    {\cos(\alpha-\beta)-\cos(\alpha+\beta)\over {\cos(\alpha+\beta)+\cos(\alpha-\beta)}}
    \]{/f}


    {part}Формулы преобразования суммы в произведение{/part}

    <p>
        Нужно уметь преобразовывать сумму и разность тригонометрических функций в их
        произведение. Если честно - эти формулы я никогда не запоминал, а всегда выводил по
        мере необходимости. Но для того,  чтобы не тратить время - можете их запомнить.
        Главное знать подход, как от суммы можно перейти к произведению, и его мы сейчас рассмотрим.
    </p>

    <p>
        Допустим нам нужно преобразовать сумму синусов в произведение, т.е. найти sin&alpha;+sin&beta;.
    </p>

    <p>
        Суть метода заключается в том, что углы &alpha; и &beta; представляются в виде суммы и разности
        углов A и B, то есть в слудеющем виде:
    </p>

    \[\left \lbrace
    \begin{aligned}
    \alpha&=A+B \\
    \beta&=A-B
    \end{aligned}\right.\]

    <p>
        Это всегда можно сделать. Действительно, как следует из предыдущей системы, A и B можно выбрать равными:
    </p>

    \[\left \lbrace
    \begin{aligned}
    A &= {\alpha+\beta\over 2} \\
    B &= {\alpha-\beta\over 2} \\
    \end{aligned}\right.\]

    <p>
        После замены &alpha; и &beta; можно уже пользоваться формулами сложения {1.5|fhref}:
    </p>

    <p>
        \(
        \begin{aligned}
        \sin\alpha+\sin\beta&=\sin(A+B)+\sin(A-B)=\\
        &=\sin A\cos B + \cos A\sin B +\sin A\cos B - \cos A\sin B = 2\sin A\cos B
        \end{aligned}
        \)
    </p>

    <p>
        Делая обратную замену, получим:
    </p>

    {f num='6.1'}\[\sin\alpha+\sin\beta=2\sin{\alpha+\beta\over 2}\cos{\alpha-\beta\over 2}\]{/f}

    <p>
        Заменив в этой формуле &beta; на &minus;&beta; и воспользовавшись нечёиностью синуса {'1.2'|fhref},
        получим формулу для преобразования разности синусов:
    </p>

    {f num='6.2'}\[\sin\alpha-\sin\beta=2\sin{\alpha-\beta\over 2}\cos{\alpha+\beta\over 2}\]{/f}

    <p>
        В более компактной форме две последние формулы записываются так:
    </p>

    \[\sin\alpha\pm\sin\beta=2\sin{\alpha\pm\beta\over 2}\cos{\alpha\mp\beta\over 2}\]

    <p>
        Совершнно аналогичным образом получим формулы преобразования суммы и разности косинусов:
    </p>

    {f num='6.3'}\[\cos\alpha + \cos\beta=2\cos{\alpha + \beta\over 2}\cos{\alpha - \beta\over 2}\]{/f}

    {f num='6.4'}\[\cos\alpha - \cos\beta=-2\sin{\alpha + \beta\over 2}\sin{\alpha - \beta\over 2}\]{/f}

    <p>
        Формулы преобразования суммы sin&alpha;+cos&beta; не существует. Если мы воспользуемся
        нашей заменой - подобные члены не сократятся и мы не получим искомое произведение:
    </p>

    <p>
        \(
        \begin{aligned}
        \sin\alpha+\cos\beta&=\sin(A+B)+\cos(A-B)=\\
        &=\sin A\cos B + \cos A\sin B + \cos A\cos B - \sin A\sin B
        \end{aligned}
        \)
    </p>

    <p>
        Как видите - сумма так и осталась суммой.
    </p>

    <p>
        Сумма тангенсов получается в одно действие:
    </p>

    {f num='6.5'}\[tg\alpha\pm tg\beta={\sin\alpha\over\cos\alpha}\pm{\sin\beta\over\cos\beta}=
    {\sin\alpha\cos\beta\pm\cos\alpha\sin\beta\over{\cos\alpha\cos\beta}}={\sin(\alpha\pm\beta)\over\cos\alpha\cos\beta}\]{/f}


    {part}Однопараметрическое представление{/part}

    <p>
        Все тригонометрические функции можно выразить через тангенс половинного угла.
    </p>

    <p>
        Мы с вами уже видели, как синус или косинус можно выразить через синус и
        косинус половинного угла: соотношения {'2.1.1'|fhref} и {'2.1.2'|fhref}. Добавив
        к этому ещё основное тригонометрическое тождество {'1.3'|fhref}, получим:
    </p>

    {f num='7.1'}
    \[
    \begin{aligned}
    \sin\alpha&={\sin\alpha\over 1}={2\sin{\alpha\over 2}\cos{\alpha\over 2}\over{\sin^2{\alpha\over 2}+\cos^2{\alpha\over 2}}}={2tg{\alpha\over 2}\over{1+tg^2{\alpha\over 2}}}\\
    \cos\alpha&={\cos\alpha\over 1}={2\sin{\sin^2{\alpha\over 2}-\cos^2{\alpha\over 2}}\over{\sin^2{\alpha\over 2}+\cos^2{\alpha\over 2}}}={{1-tg^2{\alpha\over 2}}\over{1+tg^2{\alpha\over 2}}}\\
    tg\alpha&={\sin\alpha\over\cos\alpha}= {2tg{\alpha\over 2}\over{1-tg^2{\alpha\over 2}}}
    \end{aligned}
    \]
    {/f}
    {*f num=7.1}
    \[
    \sin\alpha={\sin\alpha\over 1}={2\sin{\alpha\over 2}\cos{\alpha\over 2}\over{\sin^2{\alpha\over 2}+\cos^2{\alpha\over 2}}}=
    {2tg{\alpha\over 2}\over{1+tg^2{\alpha\over 2}}}
    \]
    {/f}
    
    {f num=7.2}
    \[
    \cos\alpha={\cos\alpha\over 1}={2\sin{\sin^2{\alpha\over 2}-\cos^2{\alpha\over 2}}\over{\sin^2{\alpha\over 2}+\cos^2{\alpha\over 2}}}=
    {{1-tg^2{\alpha\over 2}}\over{1+tg^2{\alpha\over 2}}}
    \]{/f}
    
    {f num=7.3}
    \[
    tg\alpha={\sin\alpha\over\cos\alpha}= {2tg{\alpha\over 2}\over{1-tg^2{\alpha\over 2}}}
    \]
    {/f*}


    {part}Примеры{/part}


    <p>Мы с вами рассмотрели практически все основные формулы тригонометрии. Естесстевенно всего их -
        огромное количество, всех просто не перечислить, но у нас и небыло такой задачи.
        Главное, что вы увидели - откуда они все получаются, каким образом можно с помощью одних
        получать другие и так далее.
    </p>

    <p>
        Абсолютно все формулы, которые мы сегодня получили - выведены, по большому счёту, из двух:
        основного тригонометрического тождества {'1.3'|fhref} и формул сложения для синуса и косинуса:
        {'1.5'|fhref}.
    </p>

    <p>
        С каждой новой рещённой задачей ваша математическая энтуиция будет оттачиваться и
        нужные формулы будут вспоминаться сами, как по моновению волшебной палочки;) Забыв нужную
        формулу - вы уже не растеряетесь, а просто выведете её на основе одной из формул, описанных нами
        в первом разделе.
    </p>

    {ex num=1}
    {head}
    Выразить sin2&alpha; и cos2&alpha; через tg&alpha;.
    {/head}

    <p>
        Формул таких мы не выводили, но получить их сможм без проблем.
    </p>

    \(\sin 2\alpha=2\sin\alpha\cos\alpha=2\sin\alpha\cos\alpha{\cos\alpha\over\cos\alpha}=
    2{\sin\alpha\over\cos\alpha}\cos^2\alpha={2tg\alpha\over{1+tg^2\alpha}}\)

    <p>
        В последнем преобразовании мы воспользовались соотношением {1.4|fhref}.
    </p>

    \(\cos 2\alpha=2\cos^2\alpha-1={2\over{1+tg^2\alpha}}-1={1-tg^2\alpha\over{1+tg^2\alpha}}\)

    <p>
        Кстати, убедимся в справедливости ранее полученного соотношения {2.3|fhref}:
    </p>

    \(tg 2\alpha={\sin 2\alpha\over\cos 2\alpha}={2tg\alpha\over{1-tg^2\alpha}}\)

    {ans}
    <p>\(\sin 2\alpha={2tg\alpha\over{1+tg^2\alpha}}\)</p>
    <p>\(\cos 2\alpha={1-tg^2\alpha\over{1+tg^2\alpha}}\)</p>
    {/ans}
    {/ex}



    {ex num=2}
    {head}
    Дркажите тождество
    \[tg 2\alpha+ctg 2\alpha+tg 6\alpha+ctg 6\alpha={8\cos^2{4\alpha}\over\sin 12\alpha}\]
    {/head}

    <p>
        По определению тангенса и котангенса {1.1|fhref}, имеем:
    </p>

    <p>
        \(A=tg 2\alpha+ctg 2\alpha+tg 6\alpha+ctg 6\alpha =
        {\sin 2\alpha\over\cos 2\alpha}+{\cos 2\alpha\over\sin 2\alpha} +
        {\sin 6\alpha\over\cos 6\alpha} + {\cos 6\alpha\over\sin 6\alpha}=\)
    </p>

    <p>
        \(={{\sin^2 2\alpha+\cos^2 2\alpha}\over {\sin 2\alpha\cos 2\alpha}}+
        {{\sin^2 6\alpha+\cos^2 6\alpha}\over {\sin 6\alpha\cos 6\alpha}}=
        {1\over {\sin 2\alpha\cos 2\alpha}}+{1\over {\sin 6\alpha\cos 6\alpha}}=\)
    </p>

    <p>
        \(={2\over{\sin 4\alpha}}+{2\over{\sin 12\alpha}}=
        {2(\sin 12\alpha + \sin 4\alpha)\over{\sin 4\alpha\sin 12\alpha}}\)
    </p>

    <p>Преобразовав сумму синусов по формуле {6.1|fhref}, получим:</p>

    <p>
        \(A={{4\sin 8\alpha\cos 4\alpha}\over{\sin 4\alpha\sin 12\alpha}}=
        {{4(2\sin 4\alpha\cos 4\alpha)\cos 4\alpha}\over{\sin 4\alpha\sin 12\alpha}}=
        {{8\sin 4\alpha\cos^2 4\alpha}\over{\sin 4\alpha\sin 12\alpha}}=
        {8\cos^2{4\alpha}\over\sin 12\alpha}\)
    </p>
    {/ex}

    {ex num=3}
    {head}
    Докажите тождество
    \[(1+{1\over\cos 2\alpha}+tg 2\alpha)(1-{1\over\cos 2\alpha}+tg 2\alpha)=2tg 2\alpha\]
    {/head}

    <p>Перегруппировав слагаемые и раскрыв разность квадратов, получим:</p>

    <p>\(\bigl((1+tg 2\alpha)+{1\over\cos 2\alpha}\bigr)\bigl((1+tg 2\alpha)-{1\over\cos 2\alpha}\bigr)=
        (1+tg 2\alpha)^2-{1\over\cos^2 2\alpha}=\)
    </p>

    <p>\(=1+2tg 2\alpha+tg^2 2\alpha-{1\over\cos^2 2\alpha}=1+2tg 2\alpha+tg^2 2\alpha-(1+tg^2 2\alpha)=2tg 2\alpha\)</p>

    {/ex}

    {ex num=4}
    {head}
    Докажите тождество
    \[\sin^{-1}\alpha+tg^{-1}\alpha=ctg{\alpha\over 2}\]
    {/head}

    <p>\(\sin^{-1}\alpha+tg^{-1}\alpha={1\over\sin\alpha}+{\cos\alpha\over\sin\alpha}=
        {1+\cos\alpha\over\sin\alpha}={1\over tg{\alpha\over 2}}=ctg{\alpha\over 2}\)
    </p>

    <p>В конце мы воспользовались полученным нами ранее соотношением {4.4|fhref}.</p>

    {/ex}

    {ex num=5}
    {head}
    Докажите тождество
    \[2\sin^2(3\pi-2\alpha)\cos^2(5\pi+2\alpha)={1\over 4}-{1\over 4}\sin({5\pi\over 2}-8\alpha)\]
    {/head}

    <p>Сначала с помощью формул приведения избавимся от синусов и косинусов сумм.</p>
    <p>
        Мы знаем, что функции вида sin(&pi;n+&alpha;), cos(&pi;n+&alpha;) при выражении
        через угол &alpha; не меняются на ко-функции. Первые синус и косинус стоят в квадрате,
        поэтому в данном случае нам о знаках думать не надо, сразу получаем:
    </p>

    \(\sin^2(3\pi-2\alpha)=\sin^2{2\alpha},\,\cos^2(5\pi+2\alpha)=\cos^2 {2\alpha}\)

    <p>
        Функции вида sin(\(\pi\over2\)n+&alpha;), cos(\(\pi\over2\)n+&alpha;) при выражении
        через угол &alpha; меняются на ко-функции. Имеем:
    </p>

    \(\sin({5\pi\over 2}-8\alpha)=\sin({4\pi\over 2}+{\pi\over 2}-8\alpha)=\sin(2\pi+{\pi\over 2}-8\alpha)=\sin({\pi\over 2}-8\alpha)=\)
    \(=\cos(-8\alpha)=\cos8\alpha\)

    <p>В конце мы воспользовались периодичностью и чётностью косинуса, а также тем, что синус угла \(\pi\over2\)&minus;&alpha; положителен.</p>

    <p>Исходное тождество приведено к виду:</p>

    \(2\sin^2{2\alpha}\cos^2{2\alpha}={1\over 4}-{1\over 4}\cos 8\alpha\)

    <p>Поработаем отдельно с левой частью:</p>

    \(2\sin^2{2\alpha}\cos^2{2\alpha}={1\over 2}(2\sin 2\alpha\cos 2\alpha)^2=
    {1\over 2}\sin^2 4\alpha = {1\over 2}\Bigl({{1-\cos 8\alpha}\over 2}\Bigr) =\)
    \(={1\over 4}(1-\cos 8\alpha)\)

    <p>В последнем преобразовании были применены формулы {2.1|fhref} и {3.1|fhref}.</p>

    {/ex}

    {*
    <p>
    Поэксперементировав, вы сможете обнаружить очень интересные соотношения, например:
    \((\sin\alpha\pm\cos\alpha)^2=\sin^2\alpha\pm 2\sin\alpha\cos\alpha+\cos^2\alpha=1\pm\sin 2\alpha\).
    </p>
    *}

    {partition}TASKS{/partition}

    {task}
    <p>Найдите значение выражения: cos4&alpha; &minus; sin4&alpha;ctg2&alpha;</p>
    {solut}
    <p>Воспользуемся формулами двойного агрумента: </p>
    <p>
        (cos\sup{2}2&alpha; &minus; sin\sup{2}2&alpha;) &minus; (2sin2&alpha;cos2&alpha;(cos2&alpha;&frasl;sin2&alpha;)) =
        cos\sup{2}2&alpha; &minus; sin\sup{2}2&alpha; &minus; 2cos\sup{2}2&alpha; = &minus;cos\sup{2}2&alpha; &minus; sin\sup{2}2&alpha; = &minus;1
    </p>
    {/solut}
    {ans}
    <p>-1</p>
    {/ans}
    {/task}

    {task}
    <p>Докажите тождество:</p>

    \[{\cos(3\pi-2\alpha)\over2\sin^2\left({5\pi\over4}+\alpha\right)}=tg\left(\alpha-{5\pi\over4}\right)\]

    {solut}
    <p>
        Преобразуем все элементы уравнения:
    </p>

    \[
    \begin{aligned}
    \cos(3\pi-2\alpha)&=\cos(2\pi+\pi-2\alpha)=\cos(\pi-2\alpha)=-\cos2\alpha, \\
    2\sin^2\left({5\pi\over4}+\alpha\right)&=2\sin^2\left(\pi+{\pi\over4}+\alpha\right)=2\sin^2\left({\pi\over4}+\alpha\right)=1-\cos\left({\pi\over2}+2\alpha\right)=\\
    &=1+\sin2\alpha, \\
    tg\left(\alpha-{5\pi\over4}\right)&=tg\left(\alpha-{\pi\over4}-\pi\right)=tg\left(\alpha-{\pi\over4}\right)=-tg\left({\pi\over4}-\alpha\right) \\
    \end{aligned}
    \]


    <p>
        Преобразования, я надеюсь, Вам все уже понятны. Отмечу только, что для понижения степени синуса я воспользовался фомулой {4.1|fhref}.
    </p>

    <p>
        Итак, исходное тождество приведено к виду:
    </p>

    \[-{\cos2\alpha\over{1+\sin2\alpha}}=-tg\left({\pi\over4}-\alpha\right)\]

    <p>
        Поработаем отдельно с левой частью:
    </p>

    \[
    \begin{aligned}
    {\cos2\alpha\over{1+\sin2\alpha}}&={\cos^2\alpha-\sin^2\alpha \over{\sin^2\alpha+\cos^2\alpha-2\sin\alpha\cos\alpha}}=
    {(\cos\alpha-\sin\alpha)(\cos\alpha+\sin\alpha)\over{(\sin\alpha+\cos\alpha)^2}}=\\
    &={\cos\alpha-\sin\alpha\over{\cos\alpha+\sin\alpha}}={1-tg\alpha\over{1+tg\alpha}}
    \end{aligned}
    \]

    <p>
        Теперь к правой части применим формулу тангенса разности {1.6|fhref}, а также учтём, что tg\(\pi\over4\)=1:
    </p>

    \[
    \begin{aligned}
    tg\left({\pi\over4}-\alpha\right)={tg{\pi\over4}-tg\alpha\over{1+tg{\pi\over4}tg\alpha}}={1-tg\alpha\over{1+tg\alpha}}
    \end{aligned}
    \]

    <p>
        Как видим, получили тоже самое. Тождество доказано.
    </p>
    {/solut}
    {/task}



    {task}
    <p>Докажите тождество:</p>

    \[{\sin({\pi\over2}+3\alpha)\over{1-\sin(3\alpha-\pi)}}=ctg\left({5\pi\over4}+{3\alpha\over2}\right)\]

    {solut}
    <p>
        Сначала поработаем с левой частью, приведя синусы сумм к обычным синусам или косинусам:
    </p>

    {p bold=1}sin(\(\pi\over2\) + 3&alpha;):{/p}

    <p>
        угол \(\pi\over2\) входит в сумму нечётное кол-во раз (один), поэтому синус меняется на косинус. Угол \(\pi\over2\) + 3&alpha; лежит во втором
        квадранте (напомню, рассуждать нужно так, будто угол 3&alpha; - острый), где синус положителен, поэтому sin(\(\pi\over2\)+3&alpha;)=cos3&alpha;.
    </p>

    {p bold=1}sin(3&alpha; &minus; &pi;):{/p}

    <p>
        угол \(\pi\over2\) входит в сумму чётное кол-во раз (два), поэтому синус не меняется на косинус. Угол 3&alpha;&minus;&pi; лежит в третьем
        квадранте (опять считаем 3&alpha; острым), где синус отрицателен, поэтому {nobr}sin(3&alpha; &minus; &pi;)=&minus;sin3&alpha;.{/nobr}
    </p>

    <p>
        Итак, после замены исходное тождество примет вид:
    </p>

    \[{{\cos3\alpha}\over{1+\sin3\alpha}}=ctg\left({5\pi\over4}+{3\alpha\over2}\right)\]

    <p>
        В правой части я предлагаю сделать следующее: сначала поспользуемся тем, что котангенс - периодическая функция с периодом &pi;,
        а затем воспользуемся выражением {'4.4'|fhref} для тангеса половинного угла:
    </p>

    \[
    \begin{aligned}
    ctg\left(\pi+{\pi\over4}+{3\alpha\over2}\right)&=ctg\left({\pi\over4}+{3\alpha\over2}\right)={1\over tg\left({\pi\over2}+3\alpha\over2\right)}=
    {\sin({\pi\over2}+3\alpha)\over {1-\cos({\pi\over2}+3\alpha)}}=\\
    &={\cos3\alpha\over 1+\sin3\alpha}
    \end{aligned}
    \]

    <p>
        В правой части мы получили то-же самое, что и в левой - тождество доказано.
    </p>
    {/solut}
    {/task}



    {task}
    <p>Докажите тождество:</p>

    \[{{\sin2\alpha-\sin3\alpha+\sin4\alpha}\over{\cos2\alpha-\cos3\alpha+\cos4\alpha}}=tg3\alpha\]

    {solut}
    <p>
        В этом рпимере, конечно, нужно переходить от сложения к произведению.
        Обратим внимание на то, что в формулах {6.1|fhref} и {6.3|fhref}, позволяющих это сделать, аргументы справа имеют вид
        &frac12;(&alpha;+&beta;) и {nobr}&frac12;(&alpha;&minus;&beta;){/nobr}, что наталкивает на мысль группировать чётные углы с чётными, нечётные с
        нечётными, то есть:
    </p>

    <p>
        sin2&alpha;&minus;sin3&alpha;+sin4&alpha;=(sin2&alpha;+sin4&alpha;)&minus;sin3&alpha;=2sin3&alpha;cos&alpha;&minus;sin3&alpha;=sin3&alpha;(2cos&alpha;&minus;1)
    </p>

    <p>
        Аналогично для знаменателя:
    </p>

    <p>
        cos2&alpha;&minus;cos3&alpha;+cos4&alpha;=(cos2&alpha;+cos4&alpha;)&minus;cos3&alpha;=2cos3&alpha;cos&alpha;&minus;cos3&alpha;=cos3&alpha;(2cos&alpha;&minus;1)
    </p>

    <p>
        Очевидно, что после деления последних двух равенств друг на друга мы получим tg3&alpha;.
    </p>

    <p>
        Что касается группировки слагаемых - я рекомендую всегда так делать, чтобы после преобразования суммы в произведение у Вас получались целые углы.
    </p>
    {/solut}
    {/task}


    {task}
    <p>Докажите тождество:</p>

    \[\sin4\alpha-\sin5\alpha-\sin6\alpha+\sin7\alpha=-4\sin{\alpha\over2}\sin{11\alpha\over2}\]

    {solut}
    <p>
        В левой части сгруппируем члены так: (sin4&alpha;&minus;sin6&alpha;)+(sin7&alpha;&minus;sin5&alpha;).
        После этого воспользуемся формулой {'6.2'|fhref}, а затем {'6.4'|fhref}:
    </p>

    <p>
        (sin4&alpha;&minus;sin6&alpha;)+(sin7&alpha;&minus;sin5&alpha;)=&minus;2sin&alpha;cos5&alpha;+2sin&alpha;cos6&alpha;=2sin&alpha;(cos6&alpha;&minus;cos6&alpha;)=
        &minus;4sin&alpha;sin\(\alpha\over2\)sin11\(\alpha\over2\)
    </p>
    {/solut}
    {/task}


    {task}
    <p>Докажите тождество:</p>
    \[\cos\alpha+\cos2\alpha+\cos6\alpha+\cos7\alpha=4\cos\frac{\alpha}{2}\cos\frac{5\alpha}{2}\cos4\alpha\]
    {/task}

    {task}
    <p>Докажите тождество:</p>
    \[\cos2\alpha-\cos3\alpha-\cos4\alpha+\cos5\alpha=-4\sin\frac{\alpha}{2}\sin\alpha\cos\frac{7\alpha}{2}\]
    {/task}


    {task}
    <p>Докажите тождество:</p>
    \[\sin^2\left({15\pi\over2}-2\alpha\right)-\cos^2\left({17\pi\over2}-2\alpha\right)=-{\cos4\alpha\over\sqrt{2}}\]

    {solut}

    <p>
        Поработаем с левой частью:
    </p>

    \[
    A=\sin^2\left({15\pi\over2}-2\alpha\right)-\cos^2\left({17\pi\over2}-2\alpha\right)
    \]

    <p>
        Сначала воспользуемся периодичностью синуса и косинуса, а затем применим формулы понижения степени {4.1|fhref} и {4.2|fhref}:
    </p>

    \[
    \begin{aligned}
    \sin^2\left({15\pi\over2}-2\alpha\right)&=\sin^2\left({16\pi\over2}-{\pi\over8}-2\alpha\right)=\sin^2\left(2\pi-{\pi\over8}-2\alpha\right)=\sin^2\left({\pi\over8}+2\alpha\right)=\\
    &=\frac{1}{2}\Bigg(1-\cos\left(\frac{\pi}{4}+4\alpha\right)\Bigg)\\

    \cos^2\left({17\pi\over2}-2\alpha\right)&=\cos^2\left({16\pi\over2}+{\pi\over8}-2\alpha\right)=\cos^2\left(2\pi+{\pi\over8}-2\alpha\right)=\cos^2\left({\pi\over8}-2\alpha\right)=\\
    &=\frac{1}{2}\Bigg(1+\cos\left(\frac{\pi}{4}-4\alpha\right)\Bigg)
    \end{aligned}
    \]

    <p>
        Таким образом:
    </p>

    \[
    \begin{aligned}
    A&=\frac{1}{2}\left(1-\cos\left(\frac{\pi}{4}+4\alpha\right)\right)-\frac{1}{2}\left(1+\cos\left(\frac{\pi}{4}-4\alpha\right)\right)=\\
    &=-\frac{1}{2}\Bigg(\cos\left(\frac{\pi}{4}+4\alpha\right)+\cos\left(\frac{\pi}{4}-4\alpha\right)\Bigg)
    \end{aligned}
    \]

    <p>
        Ну а теперь раскроем косинусы суммы и разности по формуле {1.5|fhref}, а также учтём, что \(\sin\frac{\pi}{4}=\cos\frac{\pi}{4}=\frac{1}{\sqrt2}:\)
    </p>

    \[
    \begin{aligned}
    A&=-\frac{1}{2}\left(\cos\frac{\pi}{4}\cos4\alpha - \sin\frac{\pi}{4}\sin4\alpha + \cos\frac{\pi}{4}\cos4\alpha+\sin\frac{\pi}{4}\sin4\alpha\right)=\\
    &=-\frac{1}{2}\left(2\cos\frac{\pi}{4}\cos4\alpha\right)=-{\cos4\alpha\over\sqrt{2}}
    \end{aligned}
    \]
    {/solut}
    {/task}


    {task}
    <p>Докажите тождество:</p>
    \[\cos4\alpha tg2\alpha-\sin4\alpha=\frac{2tg\alpha}{tg^2\alpha-1}\]
    {hint}
    <p>
        Представьте тангенс как отношение синуса к косинусу и приведите разность к общему знаменателю, после чего в числителе вы обызательно разглядите одну из формул {1.5|fhref};)
    </p>
    {/hint}
    {/task}



    {task}
    <p>Найдите \(tg\frac{x}{2}\), если известно, что sin\~x&minus;cos\~x=1,4</p>
    {solut}

    <p>Выразив sin\~x и cos\~x через tg\(\frac{x}{2}\) по формулам половинного аргумента {7.1|fhref}, получим:</p>

    \[{2tg\frac{x}{2}\over{1+tg^2\frac{x}{2}}}-{1-tg^2\frac{x}{2}\over{1+tg^2\frac{x}{2}}}=1,4\]

    <p>Обозначив tg\(\frac{x}{2}\)=z, получаем квадратное уравнение: z\sup{2}&minus;5z+6=0, корнями которого являются числа z\sub{1}=2 и z\sub{2}=3.</p>

    {/solut}
    {ans}
    <p>tg\(\frac{x}{2}\)=2 и tg\(\frac{x}{2}\)=3</p>
    {/ans}
    {/task}



    {partition}FINAL{/partition}

    <p>
        Если контрольные вопросы и задачи не составили для вас затруднений, то могу смело вас
        поздравить - скорее всего тригонометрия не будет больше для вас представлять
        особенных сложностей.
    </p>

    <p>
        За три лекции по тригонометрии, конечно, мы рассмотрели далеко не всё - за бортом остались
        такие темы, как: графики тригонометрических функций, тригонометрические уравнения и системы,
        тригонометрические неравенства.
        Тем не менее, если всё то, о чём мы с вами говорили, вами усвоено - всё остально не
        вызовет никаких проблем.
    </p>

    <p>
        Тригонометрические неравенства и уравнения встречаются на экзаменах постоянно.
        Для тех, кому действительно интересно всё то, о чём мы говорим, я разработал
        специальный юнит. Ссылка внизу.
    </p>

{/if}
