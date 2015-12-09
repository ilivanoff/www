{if $data}
    {assign var=zip value=$data.zip}
    {text}
    Формул: {$data.formules|@count} ({$data.imagesSize|fsize})
    <a href="{$zip->getRelPath()}">{$zip->getAbsPathWin()} ({$zip->getSize()|fsize})</a>
    {/text}
    {foreach from=$data.formules item=img}
        <div class="img-box">
            <span class="num">
                {$img@iteration}.
            </span>
            <div class="info">
                {$img|dirimg_info}
            </div>
        </div>        
    {/foreach}
{else}
    <div class="no_items">Нет формул</div>
{/if}