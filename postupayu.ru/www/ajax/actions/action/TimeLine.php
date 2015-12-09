<?php

class TimeLine extends AbstractAjaxAction {

    const PARAM_LIDENT = 'lident';
    const PARAM_EIDENT = 'eident';

    protected function getAuthType() {
        return AuthManager::AUTH_TYPE_NO_MATTER;
    }

    protected function isCheckActivity() {
        return false;
    }

    protected function getRequiredParamKeys() {
        return array(self::PARAM_LIDENT);
    }

    protected function executeImpl(ArrayAdapter $params) {
        $lident = $params->str(self::PARAM_LIDENT);
        $eident = $params->str(self::PARAM_EIDENT);

        if ($params->bool(TIMELINE_LOADING_MARK)) {
            /*
             * Загружаем представление элемента хронологической шкалы
             */
            return new AjaxSuccess(TimeLineManager::inst()->getTimeLineItemPresentation($lident, $eident, $params));
        } else {
            /*
             * Загружаем композицию
             */
            return new AjaxSuccess(TimeLineManager::inst()->getTimeLineJson($lident, $params));
        }
    }

}

?>