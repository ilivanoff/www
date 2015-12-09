{assign var="postContent" value=$postCP->getPost()}
<div class="bp">
    {$postCP|post_header}
    {img post=$postCP->getPost() class='cover'}
    {$postCP|post_content}
    {$postCP|post_bottom}
    {$postCP|post_discussion}
</div>