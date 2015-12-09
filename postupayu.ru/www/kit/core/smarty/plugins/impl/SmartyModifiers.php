<?php

class SmartyModifiers extends AbstractSmartyFunctions {

    public static function post_anons_placeholder($post) {
        if ($post instanceof PostContentProvider) {
            $post = $post->getPost();
        }
        $params['class'] = $post->getPostType() . '-anons-placeholder';
        $params['data'] = array('id' => $post->getId());
        return PsHtml::div($params, 'Идёт построение списка...');
    }

}
?>