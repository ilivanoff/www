{if $error}
    {$error}
{else}
    {if $type=='is'}
        {$full}
    {else}
        <h1>Короткий вид</h1>
        {$short}
        <br/>
        <h1>Полный вид</h1>
        {$full}
    {/if}
{/if}