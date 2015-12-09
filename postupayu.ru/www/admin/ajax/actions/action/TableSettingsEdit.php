<?php

class TableSettingsEdit extends AbstractAdminAjaxAction {

    protected function getRequiredParamKeys() {
        return array('action');
    }

    protected function executeImpl(ArrayAdapter $params) {
        $action = $params->str('action');

        switch ($action) {
            case 'saveIni':
                $errors = PsDbIniHelper::validateAndSaveDbIniContent($params->str('scope'), $params->str('content'));
                if ($errors) {
                    return implode('<br>', $errors);
                }
                break;

            case 'saveProps':
                $errors = PsDbIniHelper::validateAndSaveDbIniTableProps($params->str('scope'), $params->arr('tables'));
                if ($errors) {
                    return implode('<br>', $errors);
                }
                break;

            case 'dataExport':
                TableExporter::inst()->exportTableData($params->str('table'));
                break;

            case 'acceptDiff':
                TableExporter::inst()->acceptDiff($params->str('table'), $params->str('ident'));
                break;

            case 'acceptAllDiffs':
                TableExporter::inst()->acceptAllDiffs($params->str('table'));
                break;

            default:
                raise_error("Неизвестный тип действия: [$action]");
        }


        return new AjaxSuccess();
    }

}

?>