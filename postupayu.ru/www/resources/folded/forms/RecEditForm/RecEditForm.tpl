<form id="{$form_id}" action="" method="post">
    {$html_hiddens}
    {if isset($table)}
        <fieldset>
            {foreach $table->getColumns() as $id=>$column}
                {$column->htmlInput($rec, $hiddens.form_action)}
            {/foreach}

            {$html_buttons}
        </fieldset>
    {/if}
</form>