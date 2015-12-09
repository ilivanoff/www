<li{$liId} class="{$liClasses}">
    {$avatar}
    <div class="{$divClasses}" {$divData}>
        {if $new}
            <a class="known" href="#{$msgId}" title="Новое сообщение"></a>
        {/if}
        <div class="comment-content">
            <div class="meta">
                <a href="#" class="author">{$userName}</a>
                <span class="date">{$msg->getDtEvent()}</span>
                <span class="controls">{$controlsTop}</span>
            </div>
            {if not $msg->isDeleted() && $msgCtt->getTheme()}
                <h4>{$msgCtt->getTheme()}</h4>
            {/if}
            <div class="comment-text">{if not $msg->isDeleted()}{$msgCtt->getContent()}{/if}</div>
            {$controlsBottom}
        </div>        
    </div>        
    {if $msg->hasChilds()}
        <ul>
            {foreach $msg->getChilds() as $child}
                {$builder->buildLeaf($child, $simple)}
            {/foreach}
        </ul>
    {/if}
</li>