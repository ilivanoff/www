<?php

abstract class Verse {

    protected $content;
    protected $poetIdent;
    protected $verseIdent;

    protected function __construct($poetIdent, $verseIdent) {
        $this->poetIdent = $poetIdent;
        $this->verseIdent = $verseIdent;
    }

    public static function inst($poetIdent, $verseIdent) {
        if ($verseIdent instanceof FoldedInfoTpl) {
            return new VerseImpl($poetIdent, $verseIdent);
        }
        return new VerseEmpty($poetIdent, $verseIdent);
    }

    public function getPoetIdent() {
        return $this->poetIdent;
    }

    public function getVerseIdent() {
        return $this->verseIdent;
    }

    /**
     * Возвращает сущность фолдинга данного поэта.
     * 
     * @return FoldedEntity
     */
    public function getFoldedEntity() {
        return PoetsManager::inst()->getFoldedEntity($this->poetIdent);
    }

    public abstract function isValid();

    public abstract function getContent();

    public function __toString() {
        return "Verse: {$this->poetIdent}/{$this->verseIdent}";
    }

}

?>
