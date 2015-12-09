<span class="controls">
    {foreach $items as $ident=>$item}
        <a href="#{$ident}" class="ip-opener" title="{$item->getName()}">{ipimg ident=$ident}</a>
    {/foreach}
</span>