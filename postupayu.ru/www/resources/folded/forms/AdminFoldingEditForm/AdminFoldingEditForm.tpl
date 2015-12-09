<form id="{$form_id}" action="{$ajax_url}" method="post">
    {$html_hiddens}

    {if isset($covers) && $covers}
        <fieldset>
            <legend class="toggle">Обложка</legend>
            {img group=$hiddens.ftype type=$hiddens.fsubtype ident=$hiddens.fident class="cover"}
            {html_input type='file' label='Обложка'}
        </fieldset>
    {/if}

    {if isset($types)}
        {if in_array('tpl', $types)}
            <fieldset>
                <legend class="toggle">Tpl</legend>
                {html_input id='tpl' type='textarea' label='' codemirror='html'}
            </fieldset>
        {/if}

        {if in_array('php', $types)}
            <fieldset>
                <legend class="toggle">php</legend>
                {html_input id='php' type='textarea' label='' codemirror='php'}
            </fieldset>
        {/if}

        {if in_array('js', $types)}
            <fieldset>
                <legend class="toggle">Javascript</legend>
                {html_input id='js' type='textarea' label='' codemirror='js'}
            </fieldset>
        {/if}

        {if in_array('css', $types)}
            <fieldset>
                <legend class="toggle">Css</legend>
                {html_input id='css' type='textarea' label='' codemirror='css'}
            </fieldset>
        {/if}

        {if in_array('print_css', $types)}
            <fieldset>
                <legend class="toggle">Print.Css</legend>
                {html_input id='print_css' type='textarea' label='' codemirror='css'}
            </fieldset>
        {/if}

        {if in_array('txt', $types)}
            <fieldset>
                <legend class="toggle">Txt</legend>
                {html_input id='txt' type='textarea' label='' codemirror='txt'}
            </fieldset>
        {/if}

        {if $table}
            <fieldset>
                <legend class="toggle">Запись в таблице {$table->getName()}</legend>
                {if $row}
                    <table class="colored database">
                        <thead>
                            <tr>
                                {foreach $table->getColumns() as $id=>$col}
                                    {if $col->isPk()}
                                        <th class="fetched"></th>
                                    {else}
                                        {if $col->isVisibleInTable()}
                                            <th>{$id}</th>
                                        {/if}
                                    {/if}
                                {/foreach}
                                <th class="fetched"></th>
                                <th class="fetched"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                {foreach $table->getColumns() as $id=>$col}
                                    {if $col->isVisibleInTable()}
                                        <td>{$col->safe4show($row[$id])}</td>
                                    {/if}
                                {/foreach}
                                <td class="td-ctrl edit">
                                    <a href="{AP_APRecEdit::urlRecEdit($table, $row)}" target="_blank">ред</a>
                                </td>
                                <td class="td-ctrl delete">
                                    <a href="{AP_APRecEdit::urlRecDelete($table, $row)}" target="_blank">уд</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                {else}
                    <div class="no_items">
                        Запись не зарегистрирована в базе
                        <a href="{AP_APRecEdit::urlRecFolding($hiddens.ftype, $hiddens.fsubtype, $hiddens.fident)}" class="clickable" target="_blank">
                            <img src="/resources/images/icons/figure/add.png"/>
                        </a>
                    </div>
                {/if}
            </fieldset>
        {/if}
    {/if}

    {$html_buttons}

</form>