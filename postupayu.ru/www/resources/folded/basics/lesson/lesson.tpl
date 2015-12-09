{assign var="postContent" value=$postCP->getPost()}
<div class="tr">
    {$postCP|post_header}
    {$postCP|post_content}
    {$postCP|post_bottom}
    {$postCP|post_discussion}
</div>