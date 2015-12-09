<form id="{$form_id}" action="{$ajax_url}" method="post">
    {$html_hiddens}
    <fieldset>
        <!--<legend>Оставьте своё сообщение</legend>-->
        {html_input type='user'}
        {notauthed}
        {html_input type='text' id='r_contacts' label='Ваши контакты'}
        {/notauthed}
        {html_input type='text' id='theme' label='Тема сообщения'}
        {html_input type='textarea'}
        {$html_buttons}
    </fieldset>
</form>