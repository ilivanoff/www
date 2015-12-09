<h4>Глобальные настройки системы:</h4>

<table id="globals" class="colored editable highlighted">
    {foreach $props as $name=>$prop}
        <tr>
            <td>{$prop->getComment()}</td>
            <td>{$name}</td>
            <td data-tdid="{$name}" class="editable fetched {$prop->getEditType()}">
                {$prop->getValue()}
            </td>
        </tr>
    {/foreach}
</table>

<div class="ctrl">
    <button class="save">Сохранить</button>
    <button class="reload">Перезагрузить</button>
</div>
