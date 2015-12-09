<?php

class CB_adminnewcomments extends BaseClientBox {

    public function getAuthType() {
        return AuthManager::AUTH_TYPE_AUTHORIZED_AS_ADMIN;
    }

    protected function getClientBoxFilling() {
        $comments = AdminManager::inst()->getPostsWithUncheckedCommentsHtml();
        $comments = $comments ? $comments : PsHtml::div(array('class' => 'no_items'), 'Нет новых комментариев');
        return new ClientBoxFilling('Новые комментарии:', false, null, array('comments' => $comments));
    }

}

?>