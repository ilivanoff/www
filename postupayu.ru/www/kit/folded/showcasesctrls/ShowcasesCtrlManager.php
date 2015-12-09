<?php

/**
 * Менеджер по управлению плагинами для предпросмотра постов
 *
 * @author azazello
 */
class ShowcasesCtrlManager extends ShowcasesCtrlFoldedResources implements PanelFolding {

    const LIST_BASE = 'base'; //Базовые варианты предпросмотра постов

    /** @var SimpleDataCache */

    private $CACHE;

    /**
     * Контроллеры, которые будут добавлены на каждую страницу спросмотра списка постов.
     */
    public function getBaseControllerIdents() {
        return $this->getListIdents(self::LIST_BASE);
    }

    /**
     * Основной метод, выполняющий построение контроллера для просмотра постов.
     * Контроллер может быть отображен в двух случаях: 
     * 1. На странице с просмотром всех постов
     * 2. На странице с рубрикой
     * 
     * @return ShowcasesControllerPanel
     */
    private function getScPanel($postType, Rubric $rubric = null) {
        $key = $postType . '-' . ($rubric ? $rubric->getIdent() : '');

        if (!$this->CACHE->has($key)) {
            $plugins[] = $this->getBaseControllerIdents();
            if ($rubric) {
                $plugins[] = Mappings::RUBRIC_2_SCCONTROLLERS($postType)->getMappedEntitys($rubric->getIdent());
            }

            $insts = $this->getUserAcessibleClassInsts(to_array_expand($plugins));
            $ctxt = new ShowcasesControllerCtxt($rubric);
            $result = array();

            /** @var ShowcasesControllerItem */
            foreach ($insts as $ident => $inst) {
                $inst->doProcess($ctxt);
                $result[$ident] = $inst;
            }

            $this->CACHE->set($key, new ShowcasesControllerPanel($result));
        }
        return $this->CACHE->get($key);
    }

    /**
     * Панель с кнопками управления предпросмотром постов
     */

    const PANEL_SCCONTROLS = 'SCCONTROLS';

    public function buildPanel($panelName) {
        $CTXT = PageContext::inst();
        if ($CTXT->isPostsListPage() || $CTXT->isRubricPage()) {
            return $this->getScPanel($CTXT->getPostType(), $CTXT->isRubricPage() ? $CTXT->getRubric() : null);
        }
        return null;
    }

    /*
     * СИНГЛТОН
     */

    /** @return ShowcasesCtrlManager */
    public static function inst() {
        return parent::inst();
    }

    protected function __construct() {
        parent::__construct();
        $this->CACHE = new SimpleDataCache();
    }

}

?>