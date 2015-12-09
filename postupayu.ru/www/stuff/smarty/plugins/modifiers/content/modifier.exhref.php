<?php

/*
 * Ссылки на формулы в постах.
 */

function smarty_modifier_exhref($exId) {
    $exId = trim($exId);

    $num = PostFetchingContext::getInstance()->getExampleNum($exId, false);
    $elId = PostFetchingContext::getInstance()->getExampleElId($exId);

    if (!$num) {
        return PsHtml::spanErr("Ссылка на незарегистрированный пример с идентификатором '$exId'");
    }

    return PsBubble::aById($elId, "№$num", 'example');
}

?>