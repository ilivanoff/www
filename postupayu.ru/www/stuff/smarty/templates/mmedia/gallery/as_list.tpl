<h2>{$name}:</h2>
<br/>
<ol class="{$smarty.const.JS_GALLERY_LIST}">
    {foreach $images as $img}
        <li>
            <h4>{$img->getName()}</h4>
            {img name=$img->getRelPath() asis=1}
            {if $img->getDescr()}
                <div class="text">
                    {$img->getDescr()}
                </div>
            {/if}
        </li>
    {/foreach}
</ol>