<?php

function smarty_modifier_dirimg_info(DirItem $img) {
    PSSmarty::template('common/dirimg_info.tpl', array('img' => $img))->display();
}

?>
