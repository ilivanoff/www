<div class="info">
    {*Задачи, примеры, анонс*}
    {if $data->getExamplesCnt()}
        <div>Примеров рассмотрено: {$data->getExamplesCnt()}</div>
    {/if}
    {if $data->getTasksCnt()}
        <div>Задач для сам. решения: {$data->getTasksCnt()}</div>
    {/if}

    {*Тестирование*}
    {foreach $testings as $tst}
        {if $tst[0]->getTime()}
            <div>Времени на тест: {$tst[0]->getTime()} мин.</div>
        {/if}
        {if $tst[0]->getTasksCnt()}
            <div>Задач в тесте: {$tst[0]->getTasksCnt()}</div>
        {/if}
        {if $tst[1]}
            <div>Результат: {$tst[1]->asString()}</div>
            <div>{$tst[1]->asStringGr()}</div>
        {/if}
    {/foreach}

    {*Анонс*}
    <div class="tr-anons-toggler"><a href="#">[Показать содержание]</a></div>
</div>