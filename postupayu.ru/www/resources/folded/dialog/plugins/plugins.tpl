{dgimg name='default.png'}
<div class="select-holder">
    <select>
        {foreach $pages as $page}
            <option value="{$page.id}" data-url="{$page.url}" data-cover="{$page.cover}">{$page.name}&nbsp;&nbsp;</option>
        {/foreach}
    </select>
</div>