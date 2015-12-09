{if $url}
    <div class="psplugin-popup client-only">
        {ctrl_button popup=1 name='nwnd' action=$url title='В отдельном окне' hoverable=1}
    </div>
{/if}
<div class="psplugin">
    {$content}
</div>