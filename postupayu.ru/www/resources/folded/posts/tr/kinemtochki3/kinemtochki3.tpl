{if $showcase_mode}
    <p>
        На данном занятии речь пойдёт о таком занятном математатическом явлении, как матрица.
    </p>
{else}
    <p>
        Всем привет!
    </p>

    {psplugin name='kinemat'}.{/psplugin}

    <p>Коротко введение, о чём пост</p>

    {partition}PLAN{/partition}
    {ANONS}

    {partition}CONSPECT{/partition}
    {part}Пункт первый{/part}

    {postimgb dir='tr/pattern' name='image.png' id='image.png'}

    {ex}
    {head}
    <p>Условие задачи</p>
    {/head}
    <p>Решение</p>
    {ans}
    <p>Ответ</p>
    {/ans}
    {/ex}


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