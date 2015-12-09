<form id="{$form_id}" action="{$ajax_url}" method="post">
    {$html_hiddens}
    <fieldset>
        <legend>Личные данные</legend>
        {html_input id='r_name' type='text' label='Имя'}
        {html_input id='r_sex'  type='sex'  label='Пол'}
        {html_input id='r_about'  type='textarea' label='О себе'}
        {html_input id='r_contacts'  type='textarea' label='Контакты'}
        {html_input id='r_msg'  type='textarea' label='Любимая цитата'}
        {$html_buttons}
    </fieldset>
</form>