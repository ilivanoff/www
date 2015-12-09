{* ALL FOLDINGS *}
{if $mode=='list'}
    <ul class="ap-navigation">
        {foreach $foldings as $folding}
            <li><a href="{$folding.url}">{$folding.name}</a></li>
        {/foreach}
    </ul>
{/if}


{* CONTENT *}
{if $mode=='content'}
    <h3>{$folding.name}</h3>
    <table class="dbitems colored editable" data-fsubtype="{$folding.fsubtype}">
        <thead>
            <tr>
                <th width='1%'>id</th>
                <th>ident</th>
                <th>name</th>
                <th>content</th>
                <th>dt_start</th>
                <th>dt_stop</th>
                <th></th>
                <th class="fetched"></th>
            </tr>
        </thead>
        <tbody>
            {foreach $folding.data as $data}
                <tr>
                    <td data-tdid="id" class="noedit">{$data.id}</td>
                    <td data-tdid="ident" class="required">{$data.ident}</td>
                    <td data-tdid="name" class="editable">{$data.name}</td>
                    <td data-tdid="content" class="editable dialog"><span>{html_4show($data.content)}</span></td>
                    <td data-tdid="dt_start" class="editable">{$data.dt_start}</td>
                    <td data-tdid="dt_stop" class="editable">{$data.dt_stop}</td>
                    <td data-tdid="b_show" class="editable yn">{$data.b_show}</td>
                    <td><a href="{$data.editurl}" target="_blank" class="nobg">фол</a></td>
                </tr>
            {/foreach}
        </tbody>
    </table>
    <div class="ctrl">
        <button class="save">Сохранить</button><button class="add">Создать</button><button class="reload">Перезагрузить</button>
    </div>

    <br/>
    {if $tlbfe}
        {hidden name='Показать хронологическую шкалу' toggle='1'}
        <div class="separetedtb">
            <h3>Хронологическая шкала:</h3>
            {$tlbfe->fetchTplWithResources()}
        </div>
        {/hidden}
    {else}
        {notice}Для данной библиотеки нет хронологической шкалы{/notice}
    {/if}
    <br/>
    <br/>
{/if}
