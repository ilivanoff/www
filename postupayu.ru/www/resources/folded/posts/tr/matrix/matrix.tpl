{if $showcase_mode}

    <p>Текст короткой информации!</p>

{else}
    <p>
        Всем привет!
    </p>

    {psplugin name='advgraph'}.{/psplugin}

    <p>Сегодня мы с вами рассмотрим матрицы.</p>

    {partition}PLAN{/partition}
    {ANONS}

    {partition}CONSPECT{/partition}

    {part}Пункт 1{/part}

    {part}Пункт 2{/part}

    {part}Пункт 3{/part}

    {*ФОРМУЛА*}
    {f id='f1'}\[\sqrt{x}\]{/f}
    Ссылка на формулу: {'f1'|fhref}

    {*БЛОЧНАЯ КАРТИНКА*}
    {postimgp name='mult.png' id='img1'}popup{/postimgp}
    <br/>
    {postimgb name='mult.png' id='img1'}
    Ссылка на рисунок: {'img1'|ihref}

    {*ТЕОРЕМА*}
    {th id='th1'}
    {head}
    <p>
        Если a!=b, то b!=a
    </p>
    {/head}
    <p>
        Допустим сначала, что a==b...
    </p>
    {/th}

    Ссылка на теорему: {'th1'|thhref}

    {*ПРИМЕР*}
    {ex id='ex1'}
    {head}
    <p>Условие задачи</p>
    {/head}
    <p>Решение</p>
    {ans}
    <p>Ответ</p>
    {/ans}
    {/ex}

    Ссылка на пример: {'ex1'|exhref}

    <p>
        Для тех, кто недавно работает с матрицами и ещё не приобрёл необходимой интуиции в этом вопросе,
        хочу предложить следующее приложение...
    </p>
    {*psplugin name='matrixmult'}.{/psplugin*}

    {partition}TASKS{/partition}
    {*ЗАДАЧА № 1*}

    {task}
    <p>Условие задачи</p>
    {hint}
    <p>Подсказка</p>
    {/hint}
    {solut}
    <p>Решение</p>
    {/solut}
    {ans}
    <p>Ответ</p>
    {/ans}
    {/task}


    {*ЗАДАЧА № 2*}

    {*...*}

    {partition}FINAL{/partition}

    <p>
        Сегодня мы с вами ...
    </p>

    <p>
        На следующем занятии мы...
    </p>

{/if}