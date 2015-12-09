<?php

function smarty_modifier_rubric_content(RubricContentProvider $cp) {
    echo $cp->getContent();
}

?>
