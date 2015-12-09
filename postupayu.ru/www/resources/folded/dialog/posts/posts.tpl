{dgimg name='default.png'}
<div class="select-holder">
    <select>
        {foreach $posts as $ident=>$post}
            <option value="{$ident}" data-url="{$post.url}" data-cover="{$post.cover}">{$post.name}</option>
        {/foreach}
    </select>
</div>