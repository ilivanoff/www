{if empty($items)}
    <h4>Нет файлов</h4>
{else}
    <ol>
        {foreach $items as $img}
            <li>
                <h4>{$img.name}</h4>
                {table}
                <img src="{$img.rels}"/>||<img src="{$img.rel}"/>
                {/table}
            </li>
        {/foreach}
    </ol>
{/if}