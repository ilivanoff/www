<?php

class BP_office extends BasicPage {

    public static function registerWebPages() {
        WebPages::register('office.php', 'Личный кабинет', PAGE_OFFICE, PB_basic::getIdent(), AuthManager::AUTH_TYPE_AUTHORIZED, BASE_PAGE_INDEX, BASE_PAGE_INDEX);
    }

    public function doProcess(RequestArrayAdapter $params) {
        
    }

    public function buildContent() {
        $user = PsUser::inst();

        FORM_RegEditForm::getInstance()->setParam(FORM_PARAM_REG_NAME, $user->getName());
        FORM_RegEditForm::getInstance()->setParam(FORM_PARAM_REG_SEX, $user->getSex());
        FORM_RegEditForm::getInstance()->setParam(FORM_PARAM_REG_ABOUT, $user->getAboutSrc());
        FORM_RegEditForm::getInstance()->setParam(FORM_PARAM_REG_CONTACTS, $user->getContactsSrc());
        FORM_RegEditForm::getInstance()->setParam(FORM_PARAM_REG_MSG, $user->getMsgSrc());

        PsDefines::setReplaceFormulesWithImages(false);
        echo $this->getFoldedEntity()->fetchTpl(array('user' => $user, 'avatars' => $user->getAvatarsList(true)));
    }

    public function getJsParams() {
        $params['curAvatar'] = PsUser::inst()->hasAvatar() ? PsUser::inst()->getAvatarId() : PsConstJs::AVATAR_NO_SUFFIX;
        return $params;
    }

    public function getSmartyParams4Resources() {
        return array('UPLOADIFY_ENABE' => true);
    }

}

?>