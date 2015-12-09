<?php

class LibEdit extends AbstractAdminAjaxAction {

    protected function getRequiredParamKeys() {
        return array('fsubtype');
    }

    protected function executeImpl(ArrayAdapter $params) {
        $libType = $params->str('fsubtype');
        $libManager = Handlers::getInstance()->getLibManager($libType);

        $models = $params->arr('models');
        foreach ($models as $model) {
            $model['grup'] = $libType;
            $item = new LibItemDb($model);
            check_condition($item->getIdent(), 'Не передан идентификатор сущности');
            $libManager->saveLibItem($item, AdminLibBean::inst());
        }
        return new AjaxSuccess();
    }

}

?>
