<?php

function smarty_function_paging_controller($params, $smarty) {
    $pp = PageContext::inst()->getPostProcessor();
    echo $pp ? $pp->getPagingController() : null;
}

?>
