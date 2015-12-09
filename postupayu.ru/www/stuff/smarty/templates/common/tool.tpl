<div class="tool"{if $id} id="{$id}"{/if}>
    {if $as_href || $ident}
        {if $ident}
            <div class="tool_cover"><a href="#" pageIdent="{$ident}" class="clickable">{img dir='icons/tools' name=$img}</a></div>
            <div class="tool_content tool-content">
                <h4 class="tool_name"><a href="#" class="tool_popup" pageIdent="{$ident}">{$name}</a></h4>
                {$c_body}
            </div>
        {else}
            <div class="tool_cover"><a href="#" class="tool_href clickable">{img dir='icons/tools' name=$img}</a></div>
            <div class="tool_content tool-content">
                <h4 class="tool_name"><a href="#" class="tool_href">{$name}</a></h4>
                {$c_body}
            </div>
        {/if}
    {else}
        <div class="tool_cover">{img dir='icons/tools' name=$img}</div>
        <div class="tool_content tool-content">
            <h4 class="tool_name">{$name}</h4>
            {$c_body}
        </div>
    {/if}
    <div class="clearall"></div>
</div>