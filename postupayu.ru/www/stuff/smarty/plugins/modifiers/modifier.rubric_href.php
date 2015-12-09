<?php

function smarty_modifier_rubric_href(Rubric $rubric, $showCnt = false) {
    echo Handlers::getInstance()->getRubricsProcessorByPostType($rubric->getPostType())->rubricHref($rubric);
}

?>