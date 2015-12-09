<?php

/**
 *
 * @author azazello
 */
class TL_posts extends TimeLineBuilderBase {

    public function getTitle() {
        return 'Хронология публикации постов';
    }

    protected function getTimeLineBuilderParams() {
        return new TimeLineBuilderParams();
    }

    protected function buildComposition(ArrayAdapter $params) {
        $items = array();
        /* @var $pp PostsProcessor */
        foreach (Handlers::getInstance()->getPostsProcessors() as $pp) {
            foreach ($pp->getPosts() as $post) {
                $items[] = $post;
            }
        }

        $events = array();

        /* @var $post Post */
        foreach ($items as $post) {
            $postType = $post->getPostType();

            $pp = Handlers::getInstance()->getPostsProcessorByPostType($postType);

            $dt_start = DatesTools::inst()->uts2dateInCurTZ($post->getDtPublication(), 'Y-m-d');
            $rec = TimeLineItem::inst($post->getName(), IdHelper::ident($post), $dt_start);
            $rec->setImage($pp->getCoverDi($post->getIdent(), TimeLineManager::COVERS_DIM));
            $rec->setLink($pp->postUrl($post->getId()));

            switch ($postType) {
                case POST_TYPE_ISSUE:
                    $rec->setColorSchema(TimeLineItem::COLOR_SCHEMA_GREEN);
                    break;
                case POST_TYPE_BLOG:
                    $rec->setColorSchema(TimeLineItem::COLOR_SCHEMA_BLUE);
                    break;
                case POST_TYPE_TRAINING:
                    $rec->setColorSchema(TimeLineItem::COLOR_SCHEMA_RED);
                    break;
            }

            $rec->setContent($pp->getPostContentProvider($post->getId())->getPostContentShowcase()->getContent());

            $events[] = $rec;
        }

        $composite = new TimeLineItemsComposite($events);
        $composite->colorOneByOne();
        return $composite;
    }

    protected function buildPresentation($ident, ArrayAdapter $params) {
        /*
         * Построение представления элемента хронологической шкалы
         */
        return $ident;
    }

}

?>