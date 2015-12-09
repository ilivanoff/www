<?php

class PP_print extends BasePopupPage {

    public function getAuthType() {
        return AuthManager::AUTH_TYPE_NO_MATTER;
    }

    /** @var PostContentProvider */
    private $postCP;

    public function doProcess(ArrayAdapter $params) {
        $postId = $params->int('postId');
        $postType = $params->str('postType');

        $pp = Handlers::getInstance()->getPostsProcessorByPostType($postType);
        $this->postCP = $pp->getPostContentProvider($postId);
    }

    public function getTitle() {
        return $this->postCP->getPost()->getName() . ' (версия для печати)';
    }

    public function getDescr() {
        return 'Отображение поста в варианте для печати';
    }

    public function getJsParams() {
        
    }

    public function buildContent() {
        //Можно и не устанавливать, т.к. к popup окнам мы не подключаем спрайты
        PsDefines::setReplaceFormulesWithSprites(false);
        $params = array('prinvView' => $this->postCP->getPostPrintVariant());
        return $this->getFoldedEntity()->fetchTpl($params);
    }

    public function getSmartyParams4Resources() {
        return array('COMMON_CSS_MEDIA' => 'all');
    }

}

?>
