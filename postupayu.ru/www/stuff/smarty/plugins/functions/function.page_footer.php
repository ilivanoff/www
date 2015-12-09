<?php

function smarty_function_page_footer($params, Smarty_Internal_Template &$template) {
    /* @var $footerTpl Smarty_Internal_Template */
    $footerTpl = PSSmarty::template('page/footer.tpl');
    //TODO - редиректить, если тип PageBuilder отличен от basic
    $footerTpl->assign('_blank', WebPages::isCurPage(PAGE_POPUP));
    $footerTpl->display();
}

?>