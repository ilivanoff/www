<form id="{$form_id}" action="{$ajax_url}" method="post">
    {$html_hiddens}
    <fieldset>
        {html_input type='file' label='zip-файл с архивом'}
        {html_input type='yesno' label='Очистить директорию'}
        {$html_buttons}
    </fieldset>
</form>