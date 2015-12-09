<form id="{$form_id}" action="{$ajax_url}" method="post">
    {$html_hiddens}
    <fieldset>
        {html_input type='text' label='<b>Идентификатор фолдинга</b>' id='new_folding_ident'}
        {if isset($table) and $table}
            {foreach $table->getColumns() as $id=>$column}
                {if not $column->isHoldFoldingIdent($folding)}
                    {$column->htmlInput($rec, $smarty.const.PS_ACTION_CREATE)}
                {/if}
            {/foreach}
        {/if}

        {$html_buttons}
    </fieldset>
</form>