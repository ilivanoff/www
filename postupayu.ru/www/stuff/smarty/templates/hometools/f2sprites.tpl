<h4>Генерация спрайтов происходит <a href='http://ru.spritegen.website-performance.org/'  target='_blank'>здесь</a>.</h4>

<ol>
    <li>Архивируем картинки в .zip или скачиваем на этой странице</li>
    <li>Переходим на сайт, устанавливаем смещение по горизонтали и вертикали равное 2px, расположение &mdash; вертикальное (Vertical)</li>
    <li>Префикс "sprite-" меняем на "sprite-<b>ico-</b>" или "sprite-<b>tr-vectors-</b>", например</li>
    <li>Генерим, сохраняем картинку и сохраняем/обновляем соответствующий .css файл со спрайтами</li>
</ol>

<br />

<ol>
    {foreach $items as $item}
        <li>
            {text}
            <a href="output/{$item.zip->getName()}">{$item.zip->getName()}</a>
            {gray}Кол-во формул: {$item.formules|count}, размер картинок: {$item.imagesSize|fsize}, размер архива: {$item.zip->getSize()|fsize}{/gray}
            {gray}{$item.zip->getAbsPathWin()}{/gray}
            {/text}
        </li>
    {/foreach}
</ol>