<?php

class AP_APPostEdit extends BaseAdminPage {

    public function title() {
        return 'Регистрация постов в базе';
    }

    public function buildContent() {
        $RES = array();

        /* @var $pp PostsProcessor */
        foreach (Handlers::getInstance()->getPostsProcessors() as $pp) {
            $bean = $pp->dbBean();
            $type = $pp->getPostType();
            $tpls = $pp->getAccessibleTemplates();

            /* @var $value DirItem */
            foreach ($tpls as $tplDi) {
                $ident = $tplDi->getNameNoExt();
                $post = AdminPostsBean::inst()->getPostByIdent($bean, $ident);
                $tplDi->setData('post', $post);
            }

            usort($tpls, array($this, 'sortTpl'));

            $rubrics = $bean instanceof RubricsBean ? AdminPostsBean::inst()->getAllRubrics($bean) : null;

            $RES[] = array(
                'type' => $pp->getPostType(),
                'title' => $pp->postsTitle(),
                'templates' => $tpls,
                'rubrics' => $rubrics
            );
        }

        echo $this->getFoldedEntity()->fetchTpl(array('data' => $RES));
    }

    public function sortTpl(DirItem $di1, DirItem $di2) {
        /* @var $post1 Post */
        $post1 = $di1->getData('post');
        /* @var $post2 Post */
        $post2 = $di2->getData('post');
        if (!$post1 && $post2) {
            return -1;
        }
        if ($post1 && !$post2) {
            return 1;
        }
        if (!$post1 && !$post2) {
            return strcmp($di1->getNameNoExt(), $di2->getNameNoExt());
        }

        return $post1->getDtPublication() < $post2->getDtPublication() ? 1 : -1;
    }

}

?>