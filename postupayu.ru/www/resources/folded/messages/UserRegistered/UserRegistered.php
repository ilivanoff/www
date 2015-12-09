<?php

class MSG_UserRegistered extends TemplateMessage {

    protected function sendMsgImpl(TemplateMessageCtxt $ctxt) {
        $ctxt->send();
    }

    protected function decodeMsgImpl(DiscussionMsg $msg) {
        return new TemplateMessageContent(
                        'Спасибо, что Вы с нами',
                        'Уважаемый пользователь <b>' . $msg->getUserTo()->getName() . '</b>, мы очень рады, что Вы зарегистрировалиль на нашем сайте. Ура!:)');
    }

}

?>