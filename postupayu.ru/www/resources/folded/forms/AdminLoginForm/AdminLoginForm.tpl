<form id="{$form_id}" action="" class="smalForm" method="post">
    {$html_hiddens}
    <fieldset>
        {html_input type='text' id='login' label='E-mail'}
        {html_input type='pass' id='password' label='Пароль'}
        {$html_buttons}
    </fieldset>
</form>