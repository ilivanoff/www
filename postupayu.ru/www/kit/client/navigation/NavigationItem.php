<?php

class NavigationItem {

    private $href;
    private $childsName;
    private $childsList;
    private $extraData;
    private $pageCode;

    private function __construct($href, $childsName = null) {
        $this->href = $href;
        $this->childsName = $childsName;

        $this->childsList = array();
        $this->extraData = array();
        $this->pageCode = null;
    }

    /** @return NavigationItem */
    public static function byHref($href, $childsName = null) {
        return new NavigationItem($href, $childsName);
    }

    /** @return NavigationItem */
    public static function byPageCode($code, $childsName = null) {
        return self::byHref(WebPage::inst($code)->getHref(), $childsName)->setPageCode($code);
    }

    /** @return NavigationItem */
    public static function byRubricProcessor(RubricsProcessor $rp, $allPostsName = 'Все заметки', $postsInRubricName = 'Посты в рубрике', $postAnonsName = 'Навигация') {
        $item = self::byPageCode($rp->getPostsListPage()->getCode(), $allPostsName);
        //Не показываем выбранную рубрику
        $item->setExtraData('chplaceholder');
        $rubrics = $rp->getRubrics();
        $rubric2post = $rp->getPostToRubric();
        /* @var $rubric Rubric */
        foreach ($rubrics as $rubric) {
            $rubricId = $rubric->getId();
            if (array_key_exists($rubricId, $rubric2post)) {
                $item->addChild(self::byRubric($rubric, $rubric2post[$rubricId], $postsInRubricName, $postAnonsName));
            }
        }
        return $item;
    }

    /** @return NavigationItem */
    public static function byPostsProcessor(PostsProcessor $pp, $allPostsName = 'Все заметки', $postAnonsName = 'Навигация') {
        $item = self::byPageCode($pp->getPostsListPage()->getCode(), $allPostsName);
        $item->setExtraData('chplaceholder');
        $posts = $pp->getPosts();
        /* @var $post Post */
        foreach ($posts as $post) {
            $item->addChild(self::byPost($post, $postAnonsName));
        }
        return $item;
    }

    /*
     * РУБРИКИ И ПОСТЫ
     */

    /** @return NavigationItem */
    private static function byRubric(Rubric $rubric, array $posts, $postsInRubricName = 'Посты в рубрике', $postAnonsName = 'Навигация') {
        $rp = Handlers::getInstance()->getRubricsProcessorByPostType($rubric->getPostType());
        $item = self::byHref($rp->rubricHref($rubric), $postsInRubricName);
        $item->setExtraData('chplaceholder');
        $item->setRubricData($rubric);

        /* @var $post Post */
        foreach ($posts as $post) {
            $item->addChild(self::byPost($post, $postAnonsName));
        }
        return $item;
    }

    /** @return NavigationItem */
    private static function byPost(Post $post, $postAnonsName = 'Навигация') {
        $pp = Handlers::getInstance()->getPostsProcessorByPostType($post->getPostType());
        $item = self::byHref($pp->postHref($post), $postAnonsName);
        $anons = $pp->getPostContentProvider($post->getId())->getPostParams()->getAnons();
        $item->setPostData($post, $pp);
        if (empty($anons)) {
            return $item;
        }

        $item->setExtraData('chanons');

        foreach ($anons as $num => $name) {
            $item->addChild(self::byHref($pp->postHref($post, $name, "p$num")));
        }
        return $item;
    }

    /*
     * Методы
     */

    private function setRubricData(Rubric $rubric) {
        $this->setExtraData('rid', $rubric->getId());
        $this->setExtraData('ptype', $rubric->getPostType());
        $this->setExtraData('rubric');
    }

    private function setPostData(Post $post, PostsProcessor $pp) {
        $this->setExtraData('pid', $post->getId());
        $this->setExtraData('rid', $post->getRubricId());
        $this->setExtraData('ptype', $post->getPostType());
        $this->setExtraData('pdate', $post->getDtPublication());
        $this->setExtraData('pdate_dmy', $post->getDtEvent(DF_JS_DATEPICKER));
        $this->setExtraData('cover', $pp->getCoverDi4Show($post->getIdent())->getRelPath());
        $this->setExtraData('cover96x96', $pp->getCoverDi($post->getIdent(), '96x96')->getRelPath());
        $this->setExtraData('cover156x156', $pp->getCoverDi($post->getIdent(), '156x156')->getRelPath());
        $this->setExtraData('post'); //isPost
    }

    public function addChild(NavigationItem $item) {
        $this->childsList[] = $item;
    }

    /** @return NavigationItem */
    private function setExtraData($key, $value = true) {
        $this->extraData[$key] = $value;
        return $this;
    }

    /** @return NavigationItem */
    public function setPageCode($code) {
        $this->pageCode = is_numeric($code) ? 1 * $code : null;
        return $this;
    }

    /** @return NavigationItem */
    public function setNoBg() {
        return $this->setExtraData('nobg');
    }

    /**
     * Преобразует элемент навигации в структуру, пригодную для разбора в javascript.
     * При этом преобразует как себя, так и дерево потомков.
     */
    public function toArray() {
        if ($this->pageCode !== null && !WebPage::inst($this->pageCode)->hasAccess()) {
            return null;
        }

        $data = $this->extraData;
        $data['href'] = $this->href;
        //$data['url'] = $this->url;
        //$data['name'] = $this->name;

        /* @var $child NavigationItem */
        foreach ($this->childsList as $child) {
            $item = $child->toArray();
            if ($item) {
                $data['chlist'][] = $item;
            }
        }
        if (array_key_exists('chlist', $data)) {
            $data['chname'] = $this->childsName;
        }
        return $data;
    }

    /**
     * Возвращает полный список ссылок на эелемнты навигации, которые не содержат якоря #
     */
    public function getRealHrefs() {
        $result = array();
        if ($this->href && !contains_substring($this->href, '#')) {
            $result[] = $this->href;
        }
        /* @var $child NavigationItem */
        foreach ($this->childsList as $child) {
            $result = array_merge($result, $child->getRealHrefs());
        }
        return array_unique($result);
    }

}

?>