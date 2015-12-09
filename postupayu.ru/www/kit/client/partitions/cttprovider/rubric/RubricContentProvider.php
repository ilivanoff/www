<?php

abstract class RubricContentProvider {

    /** @var Rubric */
    protected $rubricContent;

    /** @var RubricsProcessor */
    protected $rp;

    function __construct(Rubric $rubricContent) {
        $this->rubricContent = $rubricContent;
        $this->rp = Handlers::getInstance()->getRubricsProcessorByPostType($rubricContent->getPostType());
    }

    /** @return Rubric */
    public function getRubric() {
        return $this->rubricContent;
    }

    /** @return RubricsProcessor */
    public function rp() {
        return $this->rp;
    }

    public abstract function getContent();
}

?>