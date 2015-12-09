<div class="bp">

    {if !$mag_mode}
        {post_href post=$cp->getPost() class='post_head'}.{/post_href}
    {/if}

    {post_href post=$cp->getPost()}{img post=$cp->getPost() class='cover'}{/post_href}

    {if !$mag_mode}
        {$cp|post_meta}
    {/if}

    {$cp|post_content_showcase}

    {if !$mag_mode}
        {$cp|post_full_read}
    {/if}

</div>