<div class="tr">
    {if !$mag_mode}
        {post_href post=$cp->getPost() class='post_head'}.{/post_href}
    {/if}

    <div class="left">
        {post_href post=$cp->getPost()}{img post=$cp->getPost()}{/post_href}
        {$cp|short_tr_info}
    </div>

    <div class="right">
        {if !$mag_mode}
            {$cp|post_meta}
        {/if}
        {$cp|post_content_showcase}

        <div class="tr-anons-info">
            <h3>Содержание урока:</h3>
            {$cp|post_anons_placeholder}
        </div>
    </div>

    {if !$mag_mode}
        {$cp|post_full_read}
    {/if}

    <div class="clearall"></div>
</div>