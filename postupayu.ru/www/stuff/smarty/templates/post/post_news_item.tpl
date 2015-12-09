{$post=$cp->getPost()}
<h3 class="title">
    {post_href post=$post}{$cp->pp()->newsTitle()}{/post_href}
</h3>
{post_href post=$post class='clickable'}{img post=$post class='cover' dim='96x'}{/post_href}
<div class="content">
    {$cp|post_meta:true}
    {$cp|post_content_showcase}
</div>
{$cp|post_full_read}
