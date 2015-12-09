{todo}
ЦЕНТРОВАТЬ СТИХИ И ВЫРАВНИВАТЬ ПО ШИРИНЕ
{/todo}
<div class="poet-tl-info">
    {libimg type='p' ident=$item->getIdent() class='cover'}
    {foreach $verses as $verse}
        <br/>
        {$verse->getContent()}
    {/foreach}
    <br/>
</div>