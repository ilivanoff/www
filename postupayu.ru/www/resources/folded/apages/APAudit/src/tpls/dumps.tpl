<div class="db-dumps">
    {if $dumps}
        <ol class="text">
            {foreach $dumps as $dump}
                <li>
                    <a href="{$dump.rel}">{$dump.name}</a> ({$dump.size|fsize})
                    {hidden toggle=true name='Комментарий'}
                    <pre>{$dump.comment}</pre>
                    {/hidden}
                </li>
            {/foreach}
        </ol>
    {/if}
</div>
