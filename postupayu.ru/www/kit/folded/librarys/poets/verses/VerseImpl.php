<?php

class VerseImpl extends Verse {

    private $infoTpl;

    public function __construct($poetIdent, FoldedInfoTpl $verseTpl) {
        parent::__construct($poetIdent, $verseTpl->getDirItem()->getNameNoExt());
        $this->infoTpl = $verseTpl;
    }

    public function getContent() {
        return $this->content = isset($this->content) ? $this->content : $this->infoTpl->fetch();
    }

    public function isValid() {
        return true;
    }

}

?>