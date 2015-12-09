<?php

/*
 * Эффект перелистываемой страницы
 * +
 * время публикации и кол-во просмотров
 * 
 */

function smarty_modifier_post_bottom(PostContentProvider $postCP) {
    $tpl = PSSmarty::template('post/bottom.tpl');
    $tpl->assign('post', $postCP->getPost());
    $tpl->display();
}

?>
