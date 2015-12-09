<form id="{$form_id}" action="{$ajax_url}" method="post">
    {$html_hiddens}
    <fieldset>
        {html_input id='r_old_pass' type='pass' label='Текущий пароль'}
        {html_input id='r_pass' type='pass' label='Новый пароль'}
        {html_input id='r_pass_conf' type='pass' label='Подтверждение пароля'}
        {$html_buttons}
    </fieldset>
</form>