<?php

class PP_todo extends BasePopupPage {

    public function getAuthType() {
        return AuthManager::AUTH_TYPE_NO_MATTER;
    }

    public function doProcess(ArrayAdapter $params) {
        PsDefines::assertProductionOff(__CLASS__);
        //Отключим нормализацию страниц, так как мы редактируем HTML
        PsDefines::setNormalizePage(false);
    }

    public function getTitle() {
        return 'Список задач';
    }

    public function getDescr() {
        return 'Список задач';
    }

    public function getJsParams() {
        
    }

    public function buildContent() {
        $file = ToDoFile::inst();
        FORM_TodoForm::getInstance()->setHidden('mtime', $file->getMtime());
        FORM_TodoForm::getInstance()->setParam('text', $file->getContents());
        return $this->getFoldedEntity()->fetchTpl(array('file' => $file));
    }

    public function getSmartyParams4Resources() {
        
    }

}

?>