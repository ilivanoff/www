<?php

class FORM_MosaicAnswerForm extends BaseStockForm implements CheckActivityForm {

    const BUTTON_SEND = 'Отправить';

    public function getAuthType() {
        return AuthManager::AUTH_TYPE_AUTHORIZED;
    }

    protected function stockType() {
        return ST_mosaic::getType();
    }

    protected function processStock(BaseStock $stock, PostArrayAdapter $adapter, $button) {
        //ОБРАБОТАЕМ КОММЕНТАРИЙ
        $comment = $adapter->str(FORM_PARAM_COMMENT);
        $error = UserInputValidator::validateShortText($comment, true, MOSAIC_ANS_MAX_LEN);
        if ($error) {
            return array(FORM_PARAM_COMMENT => $error);
        }
        $comment = UserInputTools::safeShortText($comment);

        //ВЫЗОВЕМ ДЕЙСТВИЕ ДЛЯ АКЦИИ
        return $stock->formSaveAnswer($comment);
    }

}

?>