{if $i->getCover()}
    <img src="{$i->getCover()}" class="cover"/>
{/if}
<h5 class="title">{$i->getTitle()}</h5>
{if $i->getDates()}
    <div class="dates">{$i->getDates()}</div>
{/if}
<div>{$i->getContent()}</div>