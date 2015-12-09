<?php

/**
 * Менеджер информации об учёных
 */
class ScientistsManager extends ScientistsResources {

    /**
     * Заполним ссылки на посты и, если передан параметр exists, удалим элементы, для которых нет постов.
     */
    protected function fillTimeLineItem(LibItemDb $libItem, TimeLineItem $tlItem, ArrayAdapter $params) {
        $post = BlogManager::inst()->getPostByIdent($libItem->getIdent(), false);
        if ($post) {
            $tlItem->setLink(BlogManager::inst()->postUrl($post));
        }
        return !$params->bool('exists') || !!$post;
    }

    public function getTimeLineBuilderParams() {
        return new TimeLineBuilderParams('exists');
    }

    protected function timeLineItemPresentation(LibItemDb $libItem, ArrayAdapter $params) {
        return BlogManager::inst()->getPostContentProviderByIdent($libItem->getIdent())->getPostPopupVariant();
    }

    /** @return ScientistsManager */
    public static function inst() {
        return parent::inst();
    }

}

?>