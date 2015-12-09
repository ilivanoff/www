<form id="{$form_id}" action="{$ajax_url}" method="post">
    {$html_hiddens}
    <fieldset>
        {if isset($tpl)}
            {$value=$tpl}
        {else}
            {$value=null}
        {/if}
        {html_input type="textarea" codemirror="txt" label="" val=$value id="tpl"}
        {$html_buttons}
    </fieldset>
</form>