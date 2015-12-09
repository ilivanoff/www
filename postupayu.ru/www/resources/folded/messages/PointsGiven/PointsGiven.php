<?php

class MSG_PointsGiven extends TemplateMessage {

    protected function sendMsgImpl(TemplateMessageCtxt $ctxt, $pointId = null) {
        //Убедимся, что очки действиельно даны получателю сообщения
        UserPointsManager::inst()->getPointById($pointId, $ctxt->getReceiver()->getId());
        $ctxt->send($pointId);
    }

    protected function decodeMsgImpl(DiscussionMsg $msg) {
        $pointId = $msg->getTemplateData();
        $point = UserPointsManager::inst()->getPointById($pointId, $msg->getUserTo()->getId());
        $describer = $point->getDescriber();
        return new TemplateMessageContent('Вы заработали очки', 'Причина: ' . $describer->title());
    }

}

?>