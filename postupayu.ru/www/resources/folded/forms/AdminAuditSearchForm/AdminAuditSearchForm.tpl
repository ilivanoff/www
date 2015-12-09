<form id="{$form_id}" action="{$ajax_url}" method="post">
    {$html_hiddens}
    <fieldset>
        {html_input type="select" label="Тип аудита" id="process" options=$types}
        {html_input type="select" label="Тип Действия" id="action" options=$actions hasEmpty=true}
        {html_input type="text" label="Родительское действие" id="parent_action" help="id_rec_parent"}
        {html_input type="datetime" label="Дата с" id="date_from"}
        {html_input type="datetime" label="Дата по" id="date_to"}
        {$html_buttons}
    </fieldset>
</form>