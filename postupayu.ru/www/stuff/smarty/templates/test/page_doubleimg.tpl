{if empty($images)}
    <h4>Нет дублирующихся картинок</h4>
{else}
    {foreach $images as $ident=>$imgs}
        <h1>Картинка №{$imgs@iteration} ({$ident}):</h1>
        {foreach $imgs as $img}
            <div class="img-box">
                <span class="num">
                    {$img@iteration}.
                </span>
                <div class="info">
                    {$img|dirimg_info}
                </div>
            </div>
        {/foreach}
    {/foreach}
{/if}