<div class="test" data-time="{$time}">
    <h2 class="name">{$test_name}</h2>

    <div class="start_test">
        <button>Начать тест ({$time} минут на {$tasks_cnt} задач)</button>
    </div>

    <div class="test_results">
        <div class="new">
            {*Результаты теста: x из y (z%)*}
        </div>
        {if $results}
            <div class="last">
                Последний результат: {$results->asString()}
                <div class="ctrl">
                    [<a href="#{$results->getTestingResultId()}">сбросить</a>]
                </div>
            </div>
            <div class="lastfull">
                {for $num=1 to $tasks_cnt}
                {if $results->isPassed($num)}
                    <a href="#{$num}" class="t{$num} valid">+</a>
                {else}
                    <a href="#{$num}" class="t{$num}">&minus;</a>
                {/if}
                {/for}
            </div>
        {else}
            {notauthed}
            <div class="nainfo gray">
                Для сохранения результатов тестирования &mdash; <a href="#Login">авторизуйтесь</a>
                или 
                {page_href code=$smarty.const.PAGE_REGISTRATION}зарегистрируйтесь{/page_href}.
            </div>
            {/notauthed}
        {/if}
    </div>

    <div class="test_body">

        <div class="timer">
            Времени осталось: <span class="time"></span>
        </div>

        <table class="test_nums">
            <tbody>
                <tr class="nums">
                    {for $num=1 to $tasks_cnt}
                    <td class="t{$num}">{$num}</td>
                    {/for}
                </tr>
            </tbody>
        </table>

        <div class="auto_slide">
            <label><input type="checkbox" /> Автоматически переходить к следующей задаче при выборе ответа</label>
        </div>

        <div class="tasks_slider noselect">
            <span class="left">←</span> &nbsp; <span class="pos">x</span> из {$tasks_cnt} &nbsp; <span class="right">→</span>
        </div>

        <div class="test_tasks">
            {$tasks}
        </div>

        <div class="stop_test">
            <button>Проверить результаты теста</button>
        </div>

        {if $testing}
            {authed}
            <div class="update_test_res">
                <button data-id="{$testing->getTestingId()}">Сохранить/обновить результаты теста</button>
            </div>
            {/authed}
        {/if}

    </div>
</div>