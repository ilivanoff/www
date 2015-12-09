<div class="{$smarty.const.JS_GALLERY_IMAGES}" data-id="{$id}" data-name="{$name}">
    {foreach $images as $img}
        {img name=$img->getRelPath() title=$img->getName() alt=$img->getDescr() asis=1}
    {/foreach}
</div>