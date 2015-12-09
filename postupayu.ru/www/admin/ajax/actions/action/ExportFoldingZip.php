<?php

class ExportFoldingZip extends AbstractAdminAjaxAction {

    protected function getRequiredParamKeys() {
        return array('ftype', 'fsubtype', 'fident');
    }

    protected function executeImpl(ArrayAdapter $params) {
        $ftype = $params->str('ftype');
        $fsubtype = $params->str('fsubtype');
        $fident = $params->str('fident');

        $zip = Handlers::getInstance()->getFolding($ftype, $fsubtype)->export2zip($fident);
        if (!$zip->isFile()) {
            return 'Не удалось создать архив';
        }

        return new AjaxSuccess($zip->getRelPath());
    }

}

?>