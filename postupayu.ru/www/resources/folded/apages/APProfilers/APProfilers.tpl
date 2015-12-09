{$empty=empty($profilers)}

{foreach $profilers as $name=>$stats}
    <h3><a href="{AP_APProfilers::urlProfiler($name)}" class="prof-href">{$name}</a></h3>
    {if (empty($stats))}
        <h5 class="gray">Нет статистики</h5>
    {else}
        <table class="colored profilers highlighted sortable">
            <thead>
                <tr>
                    <th>Идентификатор</th>
                    <th class="fetched">Кол-во вызовов</th>
                    <th class="fetched" data-sort-type="-1">Среднее время</th>
                </tr>
            </thead>
            <tbody>
                {foreach $stats as $ident=>$sec}
                    <tr>
                        <td>{$ident}</td>
                        <td>{$sec->getCount()}</td>
                        <td>{$sec->getAverage()}</td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    {/if}
    <br />
{/foreach}

<div class="ctrl align-center">
    {if $enabled}
        <button class="off">Отключить</button>
    {else}
        <button class="on">Включить</button>
        {if not $empty}
            <button class="reset">Очистить</button>
        {/if}
    {/if}
    <button class="reload">Перезагрузить</button>
</div>
