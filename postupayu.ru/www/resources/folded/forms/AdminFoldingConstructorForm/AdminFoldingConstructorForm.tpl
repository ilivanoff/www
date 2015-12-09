<form id="{$form_id}" action="{$ajax_url}" method="post">
    {$html_hiddens}
    <fieldset>
        {html_input type="text" label="EntityName" id="EntityName"}
        {html_input type="text" label="FoldingGroup" id="FoldingGroup"}
        {html_input type="text" label="FoldingType" id="FoldingType"}
        {html_input type="text" label="FoldingSubType" id="FoldingSubType"}
        {html_input type="text" label="FoldingClassPrefix" id="FoldingClassPrefix"}

        <h3>Ресурсы:</h3>
        <div class="folding-rtypes">
            {foreach $rtypes as $name=>$value}
                <label class="rtype">
                    <input type="checkbox" name="rtypes[]" value="{$name}"/><b>{$name}</b>
                </label>
            {/foreach}
        </div>

        <h3>Интерфейсы:</h3>
        {foreach $ifaces as $name=>$iface}
            <div class="folding-ifaces">
                <label class="iface">
                    <input type="checkbox" name="ifaces[]" value="{$name}"/><b>{$name}</b>
                    <pre>{$iface->getFileContentsNoTags()}</pre>
                </label>
            </div>
        {/foreach}

        {$html_buttons}
    </fieldset>
</form>