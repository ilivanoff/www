<div class="{$smarty.const.JS_GALLERY_BOX}{if not $lazy} open{/if}" data-id="{$id}">
    <a href="#" class="header toggler">
        <span class="info">{$name}</span>
    </a>

    <div class="content">
        <div class="images">
            {if not $lazy}
                {$boxImages}
            {/if}
        </div>
        <div class="controls">
            <a href="#" class="open toggler">Показать галерею &dArr;</a>
            <a href="#" class="close toggler">Скрыть галерею &uArr;</a>
            <span class="info">{if $cnt}картинок - {$cnt}{else}картинок нет{/if}</span>
            <a href="#" class="popup">Открыть в отдельном окне</a>
        </div>
    </div>
</div>