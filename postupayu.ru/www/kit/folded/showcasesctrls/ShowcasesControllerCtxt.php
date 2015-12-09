<?php

/**
 * Контекст для построения элемента плагина предпросмотра
 *
 * @author azazello
 */
class ShowcasesControllerCtxt {

    /** @var Rubric */
    private $rubric;

    /** Признак - в рубрике или нет */
    private $isRubric;

    /** @var RubricsProcessor */
    private $rp;

    /** В рубрике, В разделе */
    private $suffix;

    function __construct(Rubric $rubric = null) {
        $this->rubric = $rubric;
        $this->isRubric = !!$rubric;
        if ($this->isRubric) {
            $this->rp = Handlers::getInstance()->getRubricsProcessorByPostType($rubric->getPostType());
            $this->suffix = ' в ' . ps_strtolower($this->rp->rubricTitle(null, 6));
        }
    }

    /** @return RubricsProcessor */
    public function rp() {
        return $this->rp;
    }

    /** @return Rubric */
    public function getRubric() {
        return $this->rubric;
    }

    public function getIsRubric() {
        return $this->isRubric;
    }

    public function getSuffix() {
        return $this->suffix;
    }

}

?>
