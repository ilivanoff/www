<?php

class AP_APUsers extends BaseAdminPage {

    public function title() {
        $cnt = AdminUserBean::inst()->getClientsCount();
        $unreaded = FeedbackManager::inst()->getNotConfirmemMsgsCnt();
        return "Пользователи ($cnt" . ($unreaded > 0 ? "/$unreaded" : '') . ')';
    }

    public function buildContent() {
        $PARAMS['users'] = AdminUserBean::inst()->getClients();
        return $this->getFoldedEntity()->fetchTpl($PARAMS);
    }

}

?>