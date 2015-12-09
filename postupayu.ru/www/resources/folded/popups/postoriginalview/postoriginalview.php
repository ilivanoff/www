<?php

class PP_postoriginalview extends BasePopupPage {

    public function getAuthType() {
        return AuthManager::AUTH_TYPE_NO_MATTER;
    }

    public function getTitle() {
        return 'Пост с необработанными формулами';
    }

    public function getDescr() {
        return 'Отображение поста с формулами, не заменёнными на картинки';
    }

    /** @var BasePostContentProvider */
    private $postCP;

    public function doProcess(ArrayAdapter $params) {
        $postId = $params->int('postId');
        $postType = $params->str('postType');

        $this->postCP = Handlers::getInstance()->getPostsProcessorByPostType($postType)->getPostContentProvider($postId);

        check_condition(!!$this->postCP, "Not found post with id='$postId' for post type='$postType'");
    }

    public function getJsParams() {
        
    }

    public function buildContent() {
        PsDefines::setReplaceFormulesWithImages(false);
        echo $this->postCP->getPostPopupVariant();
    }

    public function getSmartyParams4Resources() {
        $RESOURCES['MATHJAX_DISABLE'] = false;
        return $RESOURCES;
    }

}

?>
