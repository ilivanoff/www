<form id="{$form_id}" action="{$ajax_url}" method="post">
    {$html_hiddens}
    <fieldset>
        {html_input id='r_name' type='text' label='Имя'}
        {html_input id='r_mail' type='text' label='E-mail'}
        {html_input id='r_sex'  type='sex'  label='Пол'}
        {html_input id='r_pass' type='pass' label='Пароль'}
        {html_input id='r_pass_conf' type='pass' label='Подтверждение пароля'}
        {$html_buttons}
    </fieldset>
</form>