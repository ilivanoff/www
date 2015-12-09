<h1><b>Тестирование адреса: {$host}</b></h1>
{text}
Количество запросов для набора статистики: {$rqcnt}
Общее время выполнения: {$time} сек.
Общее кол-во запросов: {$total}
{/text}
<br/>
{if $usesc}
    {foreach $result as $num=>$data}
        {$res = $data.res}
        {$props = $data.props}
        <h1>Тест #{$num}</h1>

        <div class="bl1">
            <h4>Настройки:</h4>
            <table>
                {foreach $props as $key=>$val}
                    <tr>
                        <td>{$key}</td><td>{if $val===false}false{else}{$val}{/if}</td>
                    </tr>
                {/foreach}
            </table>
        </div>

        <div class="bl2">
            <h4>Запросы:</h4>
            {table class="colored"}
            {foreach $res as $path=>$av}
                <a href="{$path}" target="_blank">{$path}</a>||{$av} сек.
            {/foreach}
            {/table}
        </div>
    {/foreach}
{else}
    {table class="colored"}
    {foreach $result.res as $path=>$av}
        <a href="{$path}" target="_blank">{$path}</a>||{$av} сек.
    {/foreach}
    {/table}
{/if}