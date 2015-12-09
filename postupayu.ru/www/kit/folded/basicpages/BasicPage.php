<?php

abstract class BasicPage extends FoldedClass implements WebPagesRegistrator {

    protected function _construct() {
        //do nothing...
    }

    public function getAuthType() {
        return AuthManager::AUTH_TYPE_NO_MATTER;
    }

    //Функция вызывается до построения контента страницы, чтобы иметь возможность выполнить ряд действий
    public abstract function doProcess(RequestArrayAdapter $params);

    public function getTitle() {
        $ctxt = PageContext::inst();

        if ($ctxt->isPostPage()) {
            $pp = $ctxt->getPostProcessor();
            $post = $ctxt->getPost();
            return $pp->postTitle() . ' | ' . $post->getName();
        }

        if ($ctxt->isRubricPage()) {
            $rp = $ctxt->getRubricsProcessor();
            $rub = $ctxt->getRubric();
            return $rp->rubricTitle() . ' | ' . $rub->getName();
        }

        return $ctxt->getPage()->getName();
    }

    public abstract function getSmartyParams4Resources();

    public abstract function getJsParams();

    public abstract function buildContent();
}

?>