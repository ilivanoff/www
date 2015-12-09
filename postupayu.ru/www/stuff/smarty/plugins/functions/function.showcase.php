<?php

function smarty_function_showcase($params, Smarty_Internal_Template &$template) {
    /*
     * type - тип поста
     */
    $type = Handlers::getInstance()->extractPostType($params['type']);

    /*
     * full_view - полный или "обрезанный вид"
     */
    $full_view = !isEmptyInArray('full_view', $params);

    /*
     * mag_mode - режим вставки в журнал
     */
    $mag_mode = !isEmptyInArray('mag_mode', $params);


    /*
     * post_id - уникальный код поста в базе
     * post_ident - идентификатор поста, чаще всего - название шаблона
     *
     * Один из параметров обязательно должен присутствовать.
     */
    $post_id = value_Array('post_id', $params);
    $post_ident = value_Array('post_ident', $params);

    check_condition($post_id || $post_ident, 'Не переданы уникальный код или идентификатор поста.');

    $handler = Handlers::getInstance()->getPostsProcessorByPostType($type);

    /* @var $postCP PostContentProvider */
    $postCP = $post_id ?
            $handler->getPostContentProvider($post_id) :
            $handler->getPostContentProviderByIdent($post_ident);


    if (!$postCP) {
        return PsHtml::spanErr(
                        $post_id ?
                                'Не найден ' . $handler->postTitle() . ' с кодом \'' . $post_id . '\'' :
                                'Не найден ' . $handler->postTitle() . ' с идентификатором \'' . $post_ident . '\'');
    }

    /* @var $postShowcaseTpls Smarty_Internal_Template */
    $postShowcaseTpls = $template->smarty->createTemplate("$type/post_showcase.tpl");

    $postShowcaseTpls->assign('cp', $postCP);
    $postShowcaseTpls->assign('full_view', $full_view);
    $postShowcaseTpls->assign('mag_mode', $mag_mode);
    $postShowcaseTpls->display();
}

?>
