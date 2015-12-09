{if $mode=='sprites_list'}
    <ul class="ap-navigation">
        {foreach $sprites as $name=>$sprite}
            <li class="level2">
                <a href="{AP_sprites::urlSprite($name)}" title="Спрайт будет перестроен">{$name}</a>
                <span class="small" title="Картинок / Элементов в спрайте">({count($sprite->getImages())}/{count($sprite->getSpriteItems())})</span>
                {hidden toggle=1}
                <img src="{$sprite->getImgDi()->getRelPath()}"/>
                {/hidden}
            </li>
        {/foreach}
    </ul>
{/if}

{if $mode=='sprite'}
    <h5>Картинки ({count($sprite->getImages())}):</h5>
    {foreach $sprite->getImages() as $img}
        <img src="{$img->getRelPath()}"/>
    {/foreach}
    <h5>Спрайт ({count($sprite->getSpriteItems())}):</h5>
    <img src="{$sprite->getImgDi()->getRelPath()}"/>

    {notice}Спрайт {$name} был перестроен{/notice}
{/if}