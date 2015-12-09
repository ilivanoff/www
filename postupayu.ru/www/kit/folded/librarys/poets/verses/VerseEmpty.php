<?php

class VerseEmpty extends Verse {

    public function __construct($poetIdent, $verseIdent) {
        parent::__construct($poetIdent, $verseIdent);
        $this->content = PsHtml::divErr("Стих [$verseIdent] не зарегистрирован для поэта [$poetIdent].");
    }

    public function getContent() {
        return $this->content;
    }

    public function isValid() {
        return false;
    }

}

?>
