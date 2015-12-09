<?php

class AbstractPost extends BaseDataStore {

    private $postType;

    public function __construct($type, $ident) {
        parent::__construct(is_array($ident) ? $ident : array('ident' => $ident));
        $this->postType = $type;
    }

    public function getPostType() {
        return $this->postType;
    }

    public function getIdent() {
        return $this->ident;
    }

    public function is($postType) {
        return $postType == $this->getPostType();
    }

}

?>
