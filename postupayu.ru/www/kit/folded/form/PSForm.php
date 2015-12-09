<?php

class PSForm extends PSFormResources {

    /** @return AbstractForm */
    public function getSubmittedForm() {
        return $this->getForm(PostArrayAdapter::inst()->str(FORM_PARAM_ID));
    }

    /** @return PSForm */
    public static function inst() {
        return parent::inst();
    }

}

?>