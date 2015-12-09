<?php

class RubricFoldedResources extends FoldedResources implements StorableFolding, DatabasedFolding {

    private $rp;
    protected $RESOURCE_TYPES_ALLOWED = array(self::RTYPE_JS, self::RTYPE_CSS, self::RTYPE_TPL);

    public function __construct(RubricsProcessor $rp) {
        $this->rp = $rp;
        parent::__construct();
    }

    public function getEntityName() {
        return $this->rp->rubricTitle();
    }

    public function getFoldingType() {
        return 'rubric';
    }

    public function getFoldingSubType() {
        return $this->rp->getPostType();
    }

    public function getFoldingGroup() {
        return next_level_dir('rubrics', $this->rp->getPostType());
    }

    /**
     * Возвращает менеджера рубрик для данного фолдинга
     * 
     * @return RubricsProcessor
     */
    public function rp() {
        return $this->rp;
    }

    protected function isIncludeToList($ident, $list) {
        return true;
    }

    /*
     * КАСТОМНЫЕ МЕТОДЫ
     */

    protected function onEntityChangedImpl($ident) {
        
    }

    public function getFoldedEntityPreview($ident) {
        $type = $this->rp->getPostType();
        $rubricCP = $this->rp->getRubricContentProviderByIdent($ident);
        $hasInDb = is_object($rubricCP);
        $rubric = null;
        if ($hasInDb) {
            $rubric = $rubricCP->getRubric();
        } else {
            //Сделаем его форсированно загружаемым из шаблона
            $virtualRubricParams['b_tpl'] = 1;
            $rubric = $this->rp->getVirtualRubric($ident, $virtualRubricParams);
            $rubricCP = ContentProviderFactory::getContentProvider($rubric);
        }

        //Накачиваем страницу

        $PARAMS = array('type' => $type, 'full' => '', 'error' => '');
        try {
            $PARAMS['full'] = $rubricCP->getContent();
        } catch (Exception $ex) {
            $PARAMS['error'] = ExceptionHandler::getHtml($ex);
        }

        $info = $hasInDb ? $this->rp->rubricHref($rubric) : PsHtml::gray($rubric->isVirtual() ? 'Рубрика не зарегистрирована в базе' : 'Рубрика зарегистрирована в базе, но не видна пользователю');
        $content = PSSmarty::template('rubric/folded_prev.tpl', $PARAMS)->fetch();

        return array(
            'info' => $info,
            'content' => $content
        );
    }

    public function getNextEntityIdent() {
        return $this->rp->getNextFoldingIdent();
    }

    function foldingTable() {
        return $this->rp->dbBean()->getRubricsView() . '.ident';
    }

    function dbRec4Entity($ident) {
        return $this->rp->getVirtualRubric($ident)->getDbRow();
    }

}

?>