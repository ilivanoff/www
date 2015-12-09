<?php

/*
 * Ссылки на блочные картинки.
 */

function smarty_modifier_ihref($imageId) {
    return FoldedContextWatcher::getInstance()->getImageNumeratorContext()->getBlockImgHref($imageId);
}

?>