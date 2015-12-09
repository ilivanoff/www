<form id="{$form_id}" action="{$ajax_url}" method="post">
    {$html_hiddens}
    <fieldset>
        {html_input type='textarea' maxlen=$smarty.const.MOSAIC_ANS_MAX_LEN label='Ваш ответ'}
        {$html_buttons}
    </fieldset>
</form>
