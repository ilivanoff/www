<h4>Результаты тестирования скорости загрузки страницы</h4>
<a href='{$url}' target='_blank'>{$url}</a>
<table class='colored'>
    <thead>
    <th>№</th>
    <th>Время</th>
    <th>Полное время</th>
</thead>
<tbody>
    {foreach from=$data key=num item=item}
        <tr {if $num==0}class='gray'{/if}>
            <td>{$num}</td>
            <td>{$item.time|number_format:2:",":""}</td>
            <td>{$item.total|number_format:2:",":""}</td>
        </tr>
    {/foreach}
    {*$trs*}
</tbody>
</table>

{text}
Средняя скорость: {$average|number_format:2:",":""} сек.
{/text}