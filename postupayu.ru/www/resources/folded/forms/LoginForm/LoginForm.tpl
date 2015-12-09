<form id="{$form_id}" action="{$ajax_url}" method="post" class="smalForm">
    {$html_hiddens}
    <fieldset>
        {html_input type='text' id='login' label='E-mail'}
        {html_input type='pass' id='password' label='Пароль'}
        {$html_buttons}
        <p>
            {page_href code=$smarty.const.PAGE_REGISTRATION}.{/page_href}
            {page_href code=$smarty.const.PAGE_PASS_REMIND class='remind'}Забыли пароль?{/page_href}
        </p>
    </fieldset>
</form>