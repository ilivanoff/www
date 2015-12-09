<?php

function smarty_modifier_rubric_title(RubricContentProvider $rubricCP) {
    $rubric = $rubricCP->getRubric();
    $rp = Handlers::getInstance()->getRubricsProcessorByPostType($rubric->getPostType());
    $title = $rp->rubricTitle();
    $name = $rubric->getName();

    echo "<h4>$title: <span>$name</span></h4>";
}

?>
