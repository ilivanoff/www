<?php

/*
 * Ссылки на теоремы в постах.
 */

function smarty_modifier_thhref($thId) {
    $thId = trim($thId);

    $CTXT = PostFetchingContext::getInstance();

    $num = $CTXT->getNextThNum($thId, false);

    if (!$num) {
        return PsHtml::spanErr("Ссылка на незарегистрированную теорему с идентификатором '$thId'");
    }

    return PsBubble::aById($CTXT->getThElId($thId), "№$num", 'theorem');
}

?>
