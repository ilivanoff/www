<div class="showcases_list">
    {foreach $posts_ids as $post_id}
        {showcase type=$type post_id=$post_id full_view=$full_view}
    {/foreach}
    {paging_controller}
</div>