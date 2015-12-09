<?php

class PP_timeline extends BasePopupPage {

    public function getAuthType() {
        return AuthManager::AUTH_TYPE_NO_MATTER;
    }

    public function getTitle() {
        return 'Хронологическая шкала';
    }

    public function getDescr() {
        return "Приложение позволяет наглядно увидеть: кто из великих учёных когда жил и кто был чьим современником.\nТакже приложение является удобным средством просмотра всех постов об известных людях.";
    }

    public function doProcess(ArrayAdapter $params) {
        
    }

    public function buildContent() {
        $TLM = TimeLineManager::inst();

        $insts = $TLM->getVisibleClassInsts();
        if (empty($insts)) {
            return; //Нет временных шкал для показа
        }

        $idents = array_keys($insts);
        $options = array();
        /** @var TimeLineBuilderBase */
        foreach ($insts as $ident => $inst) {
            $options[] = array('content' => $inst->getTitle(), 'value' => $this->getPageUrl(array('type' => $ident)));
        }
        usort($options, function($e1, $e2) {
                    return strcasecmp($e1['content'], $e2['content']);
                });

        $current = RequestArrayAdapter::inst()->str('type');
        $current = $current && in_array($current, $idents) ? $current : $idents[0];

        $select = PsHtml::select(array('class' => 'switcher'), $options, $this->getPageUrl(array('type' => $current)));

        /*
         * В данный момент мы определили идентификатор временной шкалы и построили элемент $select,
         * можем строить страницу.
         */
        $params['body'] = $TLM->fetchTplWithResources($current);
        $params['select'] = $select;

        return $this->getFoldedEntity()->fetchTpl($params);
    }

    public function getJsParams() {
        
    }

    public function getSmartyParams4Resources() {
        $RESOURCES['TIMELINE_ENABE'] = true;
        $RESOURCES['MATHJAX_DISABLE'] = false;
        return $RESOURCES;
    }

    public function getPopupVisibility() {
        return PopupVis::TRUE_DEFAULT;
    }

}

?>