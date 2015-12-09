<h1>Картинки, отсортированные по весу:</h1>

{foreach $images as $img}
    <div class="img-box">
        <span class="num">
            {$img@iteration}.
        </span>
        <div class="info">
            {$img|dirimg_info}
        </div>
    </div>
{/foreach}
