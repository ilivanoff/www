{*Содержимое поста*}
<div class="magReview is">
    {if $full_view}
        <h4><span>{post_href post=$cp}.{/post_href}</span></h4>
    {/if}

    {$cp|post_content_showcase}

    {if $full_view}
        {$cp|post_full_read}
    {/if}
</div>
