<table class="mappings">
    <tr>
        <td colspan="3">
            <select id="mapping-select">
                {foreach $mappings as $mhash=>$mapping}
                    <option value="{$mhash}"> [{$mapping->getMident()}] {$mapping->getDescription()} </option>
                {/foreach}
            </select>
        </td>
    </tr>

    <tr>
        <td id="left-source">
        </td>
        <td class="center">
            &hArr;
        </td>
        <td id="right-source">
        </td>
    </tr>

    <tr>
        <td>
            <div id="left-list">
            </div>
        </td>
        <td>
        </td>
        <td>
            <div id="right-list">
            </div>
        </td>
    </tr>

</table>

<div class="centered_buttons">
    {*<button class="clean">Очистить всё</button>*}
    <button class="save">Сохранить</button>
    <button class="update">Обновить</button>
</div>
