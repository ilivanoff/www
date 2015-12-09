<form id="{$form_id}" action="{$ajax_url}" method="post">
    {$html_hiddens}
    <fieldset>
        {html_input type="textarea" codemirror="htmlmixed" label="" id="text"}
        {$html_buttons}
    </fieldset>
</form>