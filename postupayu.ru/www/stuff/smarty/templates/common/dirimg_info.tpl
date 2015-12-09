{assign var='adapter' value=$img->getImageAdapter()}
{img asis=1 name=$img->getRelPath() class=$img->getData('class')}
{text}
Путь: {$img->getAbsPathWin()}
Вес: {$img->getSize()|fsize}
Размер: {$adapter->getWidth()} x {$adapter->getHeight()}
{/text}