<?php

class AdminPost extends Post {

    public function isShow() {
        return !!$this->b_show;
    }

    public function getRubricName() {
        return Handlers::getInstance()->getRubricsProcessorByPostType($this->getPostType())->getRubric($this->getRubricId())->getName();
    }

}

?>
