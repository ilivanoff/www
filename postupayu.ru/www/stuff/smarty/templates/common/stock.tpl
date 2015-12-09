{$active=$stock->isActive()}
{$type=$stock->getType()}
{$popup=$stock->popup()}
<div class="stock_body {"stock_$type"}{if not $active} past{/if}">
    <a href="#" pageIdent="{$popup}" class="name">{$stock->getName()}</a>
    <div class="stock">
        <div class="left">
            <a href="#" pageIdent="{$popup}" class="clickable">{stimg ident=$type}</a>
            <div class="status">
                {if $active}
                    {if $stock->isByDate()}
                        <div class="ps-stock-timer">{$stock->getSecondsLeft()}</div>
                    {/if}
                {else}
                    Акция завершилась
                {/if}
            </div>
        </div>
        <div class="right">
            <div class="body">
                {$body}
            </div>
        </div>
    </div>
    <div class="clearall"></div>
</div>