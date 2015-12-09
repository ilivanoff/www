<table class="{$class}">
    {foreach $items as $item}
        <tr>
            {foreach $item as $it}
                <td {if count($it)>1}class="{$it[1]}"{/if}>{$it[0]}</td>
            {/foreach}
        </tr>
    {/foreach}
</table>