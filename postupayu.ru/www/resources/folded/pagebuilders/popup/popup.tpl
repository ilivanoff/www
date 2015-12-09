<div id="popup_container">
    <div id="POPUP">
        {if $header}
            <div id="header">
                {if $list}
                    {PSHOST}
                    {*Фильтр*}
                    <input id="ToolsFilter" type="text" title="Фильтр"/>
                {else}
                    {page_href code=7 class="logo" title="К списку плагинов"}{PSHOST}{/page_href}
                    &mdash;
                    {$page->getTitle()}
                    {ctrl_button class='list' href='popup.php' action='lists' title='К списку плагинов' hoverable=1}
                {/if}
            </div>
        {/if}
        <div id="content">
            {$content}
        </div>
    </div>
    {page_footer}
</div>