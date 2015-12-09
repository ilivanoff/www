<div class="{$maincss}">
    <a href="#list" class="{$hintcss}" data-hint="Постраничный просмотр постов">{sprite name='list' nc=1}</a>
    {foreach $items as $ident=>$item}
        <a href="#{$ident}" class="{$hintcss}" data-hint="{$item->getName()}">{sprite name=$ident nc=1}</a>
        <div class="hidden-box">
            {$item->getContent()}
            {$plugins=$item->getPlugins()}
            {if is_array($plugins)}
                <div class="ps-showcases-view-plugins">
                    {foreach $plugins as $plugin}
                        {$plident=$plugin[0]}
                        {$plname=$plugin[1]}
                        <a href="#{$plident}" class="{$hintcss}" data-hint="{$plname}">{sprite name=$plident nc=1}</a>
                    {/foreach}
                </div>
            {/if}
        </div>
    {/foreach}
</div>