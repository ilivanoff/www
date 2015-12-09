<div class="element {$sym}" data-n="{$num}" data-m="{$mass|round}">
    <div class="num">{$num}</div>
    <div class="sym">{$sym}</div>
    <div class="name">{$name}</div>
    <div class="mass">{$mass}</div>
    <div class="lev">
        {foreach $levels as $lev}
            <div>{$lev}</div>
        {/foreach}
    </div>
</div>