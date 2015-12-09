<?php

/**
 * Форма AdminFoldingInfoTplEditForm
 *
 * @author Admin
 */
class FORM_AdminFoldingInfoTplEditForm extends BaseAjaxForm {

    const BUTTON_SAVE = 'Сохранить';

    public function getAuthType() {
        return AuthManager::AUTH_TYPE_AUTHORIZED_AS_ADMIN;
    }

    protected function processImpl(PostArrayAdapter $adapter, $button) {
        $entity = Handlers::getInstance()->getFoldedEntityByUnique($adapter->str('fentity'));
        $tpl = $entity->getFolding()->getInfoTpl($entity->getIdent(), $adapter->str('ftpl'));
        $content = $adapter->str('tpl');
        $tpl->getDirItem()->writeLineToFile($content, true);
        return new AjaxSuccess();
    }

}

?>
