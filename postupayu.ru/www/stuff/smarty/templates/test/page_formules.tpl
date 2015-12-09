<h1>Формулы:</h1>
{foreach from=$formules item=img}
    <div class="img-box">
        <span class="num">
            {$img@iteration}.
        </span>
        <div class="info">
            {$img|dirimg_info}
        </div>
    </div>        
{/foreach}
Полный размер: {$formules_size|fsize}
