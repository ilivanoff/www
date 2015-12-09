<?php

function smarty_function_showcases_list($params, Smarty_Internal_Template &$template) {

    $type = value_Array('type', $params);

    $postsProcessor = Handlers::getInstance()->getPostsProcessorByPostType($type);

    $posts_ids =
            array_key_exists('posts_ids', $params) ?
            $params['posts_ids'] :
            $postsProcessor->getPagePostsIds();

    /* @var $showcasesTpl Smarty_Internal_Template */
    $showcasesTpl = $template->smarty->createTemplate('common/showcases_list.tpl');
    $showcasesTpl->assign('posts_ids', is_array($posts_ids) ? $posts_ids : array());
    $showcasesTpl->assign('full_view', !isEmptyInArray('full_view', $params));
    $showcasesTpl->assign('type', $type);
    $showcasesTpl->display();
}

?>
