{if $showcase_mode}
    <p>
        На данном занятии речь пойдёт о таком занятном математатическом явлении, как матрица.
    </p>
{else}
    <p>
        Всем привет!
    </p>

    <p>Коротко введение, о чём пост</p>

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
    {postimgb name='img1.png' id='img1'}
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


    {partition}TASKS{/partition}

    {*ЗАДАЧА № 1*}

    {task from='Задача взята оттуда-то'}
    <p>Условие задачи</p>
    {hint}
    <p>Подсказка</p>
    {/hint}
    {solut}
    {varres}
    <p>Решение 1</p>
    {varres}
    <p>Решение 2</p>
    {/solut}
    {ans}
    <p>Ответ</p>
    {/ans}
    {proof}
    <p>Доказательство</p>
    {/proof}
    {/task}


    {task}
    <p>Условие задачи</p>
    {from}Олимпиада "ФИЗТЕХ", 2009 г.{/from}
    {solut}
    {varres}
    <p>Решение 1</p>
    {/solut}
    {proof}
    <p>Доказательство</p>
    {/proof}
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