<?php

class AP_APLibEdit extends BaseAdminPage {

    const MODE_FOLDINGS_LIST = 'list';
    const MODE_FOLDING_CONTENT = 'content';
    const MODE_FOLDING_EDIT = 'edit';

    public function title() {
        return 'Редактирование библиотек';
    }

    private function url($params = null) {
        return AdminPagesManager::getInstance()->pageUrl(self::getPageIdent(), $params);
    }

    public function buildContent() {
        $navigation = AdminPageNavigation::inst();

        $ftype = LibResources::LIB_FOLDING_TYPE;
        $fsubtype = RequestArrayAdapter::inst()->str('fsubtype');
        $fident = RequestArrayAdapter::inst()->str('fident');

        $mode = !$fsubtype ? self::MODE_FOLDINGS_LIST : self::MODE_FOLDING_CONTENT;

        $PARAMS['error'] = null;

        switch ($mode) {
            case self::MODE_FOLDINGS_LIST:
                $PARAMS['foldings'] = array();
                /* @var $manager FoldedResources */
                foreach (Handlers::getInstance()->getLibManagers() as $manager) {
                    $PARAMS['foldings'][] = array(
                        'name' => $manager->getEntityName() . ' (' . $manager->getFoldingGroup() . ')',
                        'url' => $this->url(array('fsubtype' => $manager->getFoldingSubType()))
                    );
                }

                $navigation->setCurrent('Список библиотек');
                break;

            case self::MODE_FOLDING_CONTENT:
                $manager = Handlers::getInstance()->getTimeLineFolding($fsubtype);
                $PARAMS['tlbfe'] = $manager->getTLBuilderFoldedEntity();
                $PARAMS['folding']['name'] = $manager->getEntityName();
                $PARAMS['folding']['fsubtype'] = $manager->getFoldingSubType();
                //TODO - вынести
                $items = AdminLibBean::inst()->getAllNoFetch($fsubtype);
                $PARAMS['folding']['data'] = array();
                /* @var $item LibItemDb */
                foreach ($items as $item) {
                    $item['editurl'] = AP_APFoldingEdit::urlFoldingEdit(FoldedResources::unique($ftype, $fsubtype, $item['ident']));
                    $PARAMS['folding']['data'][] = $item;
                }

                $navigation->addPath($this->url(), 'Список библиотек');
                $navigation->setCurrent($manager->getEntityName());
                break;
        }

        $PARAMS['mode'] = $mode;
        echo $this->getFoldedEntity()->fetchTpl($PARAMS);
    }

    public function getSmartyParams4Resources() {
        return array('MATHJAX_DISABLE' => true);
    }

}

?>