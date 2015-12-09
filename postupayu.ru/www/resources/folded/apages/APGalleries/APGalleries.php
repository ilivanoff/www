<?php

class AP_APGalleries extends BaseAdminPage {

    const MODE_LIST = 'list';
    const MODE_LAZY = 'lazy';
    const MODE_GALL = 'gall';

    public function title() {
        return 'Менеджер галерей';
    }

    private static function url($mode, array $params = array()) {
        $params['mode'] = $mode;
        return self::pageUrl($params);
    }

    public static function urlList() {
        return self::url(self::MODE_LIST);
    }

    public static function urlLazy() {
        return self::url(self::MODE_LAZY);
    }

    public static function urlGall($gallery) {
        return self::url(self::MODE_GALL, array('gall' => $gallery));
    }

    public function buildContent() {
        $navigation = AdminPageNavigation::inst();

        $RQ = GetArrayAdapter::inst();

        $mode = $RQ->get('mode', self::MODE_LIST);
        $PARAMS['mode'] = $mode;
        $PARAMS['galls'] = PsGallery::allInsts();

        switch ($mode) {
            case self::MODE_LIST:
                $navigation->setCurrent('Все галереи');
                break;
            case self::MODE_LAZY:
                $navigation->addPath(self::urlList(), 'Все галереи');
                $navigation->setCurrent('Поздняя загрузка');
                break;
            case self::MODE_GALL:
                $PARAMS['info'] = PsGallery::inst($RQ->str('gall'));
                $PARAMS['items'] = $PARAMS['info']->getAllGalleryItems();

                $navigation->addPath(self::urlList(), 'Все галереи');
                $navigation->setCurrent($RQ->str('gall'));
                break;
        }

        echo $this->getFoldedEntity()->fetchTpl($PARAMS);
    }

    public function getSmartyParams4Resources() {
        return array('MATHJAX_DISABLE' => true, 'UPLOADIFY_ENABE' => true);
    }

}

?>