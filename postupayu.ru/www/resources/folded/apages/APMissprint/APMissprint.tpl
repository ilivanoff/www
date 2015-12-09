{if empty($mp)}
    <div class="no_items">Опечаток нет</div>
{else}
    <h2>Опечатки</h2>
    <ol class="missprints">
        {foreach $mp as $arr}
            <li>
                <h4>{$arr.note}</h4>
                <div class="content">
                    {$arr.text}
                </div>
                <div class="ctrl">
                    {psctrl id=$arr.id_missprint delete='Удалить'}
                </div>
                <br/>
                <a class="href" href="{$arr.url}" target="_blank">{$arr.url}</a>
            </li>
        {/foreach}
    </ol>
{/if}