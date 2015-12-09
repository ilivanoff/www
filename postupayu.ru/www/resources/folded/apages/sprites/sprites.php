<?php

class AP_sprites extends BaseAdminPage {

    const MODE_SPRITES_LIST = 'sprites_list';
    const MODE_SPRITE = 'sprite';

    private static function getUrl($mode, array $params = array()) {
        $params['mode'] = $mode;
        return self::pageUrl($params);
    }

    public static function urlSpritesList() {
        return self::getUrl(self::MODE_SPRITES_LIST);
    }

    public static function urlSprite($name) {
        return self::getUrl(self::MODE_SPRITE, array('name' => $name));
    }

    public function title() {
        return 'Спрайты';
    }

    public function buildContent() {
        $RQ = RequestArrayAdapter::inst();

        $mode = $RQ->str('mode', self::MODE_SPRITES_LIST);
        $name = $RQ->str('name');

        $navigation = AdminPageNavigation::inst();

        $sprites = CssSpritesManager::getAllDirsSptites();

        switch ($mode) {
            case self::MODE_SPRITE:
                if (!array_key_exists($name, $sprites)) {
                    $mode = self::MODE_SPRITES_LIST;
                    break;
                }
                $smartyParams['sprite'] = CssSpritesManager::getSprite($name)->rebuild();
                break;
        }

        switch ($mode) {
            case self::MODE_SPRITES_LIST:
                $navigation->setCurrent('Список спрайтов');
                break;
            case self::MODE_SPRITE:
                $navigation->addPath(self::urlSpritesList(), 'Список спрайтов');
                $navigation->setCurrent($name);
                break;
        }

        $smartyParams['mode'] = $mode;
        $smartyParams['name'] = $name;
        $smartyParams['sprites'] = $sprites;

        return $this->getFoldedEntity()->fetchTpl($smartyParams);
    }

}

?>