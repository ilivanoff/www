<?php

class MSG_pattern extends TemplateMessage {

    protected function sendMsgImpl(TemplateMessageCtxt $ctxt, $param1 = null, $param2 = null) {
        $ctxt->send();
    }

    protected function decodeMsgImpl(DiscussionMsg $msg) {
        return new TemplateMessageContent($msg->getTheme(), $msg->getContent());
    }

}

?>