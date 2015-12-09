<table class="ps-sortable-compare TeX-no-tooltip">
    <tr>
        <td class="ul1">
            <ul>
                {foreach $strings as $str}
                    <li><div>{$str.l}{$str.s}</div></li>
                {/foreach}
            </ul>
        </td>
        <td class="ul2">
            <ul>
                {foreach $strings as $str}
                    <li><div>{$str.r}</div></li>
                {/foreach}
            </ul>
        </td>
    </tr>
    <tr>
        <td class="ctrl" colspan="2">
            <div>
                <button class="lock">Редактировать</button>
                <button class="shuffle">Перемешать</button>
                <button class="check">Проверить</button>
            </div>
        </td>
    </tr>
</table>