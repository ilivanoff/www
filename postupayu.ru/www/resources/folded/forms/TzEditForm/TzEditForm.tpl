<form id="{$form_id}" action="{$ajax_url}" method="post">
    {$html_hiddens}
    <fieldset>
        {html_input type='timezone'}

        {hidden text='Нужна помощь в выборе временной зоны?' toggle=1}
        {text id='tz_helper'}
        Ваш часовой пояс: <b><span class="local_tz"></span></b>.
        Ему соответствуют следующие временные зоны:
        <div class="local_tz_select"></div>
        {/text}
        {/hidden}

        {$html_buttons}
    </fieldset>
</form>