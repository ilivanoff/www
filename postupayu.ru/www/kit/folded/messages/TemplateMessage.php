<?php

abstract class TemplateMessage extends FoldedClass {

    protected function _construct() {
        
    }

    public final function getAuthType() {
        return AuthManager::AUTH_TYPE_NO_MATTER;
    }

    /** @return TemplateMessage Description */
    public static function inst() {
        return parent::inst();
    }

    /**
     * Метод выполняет фактическую отправку сообщения пользователю.
     * На вход методу может быть передан произвольный набор параметров.
     * Отправитель должен обработать их, после чего они будут записаны в v_template.
     */
    protected abstract function sendMsgImpl(TemplateMessageCtxt $ctxt);

    /**
     * Задача метода - декодировать шаблонное сообщение. Всё, что нужно, есть у DiscussionMsg.
     * 
     * @return TemplateMessageContent
     */
    protected abstract function decodeMsgImpl(DiscussionMsg $msg);

    /**
     * Основная функция, отправляющая сообщение от одного пользователя - другому.
     * На вход, помимо автора и адресата, можно передать произвольное кол-во параметров,
     * все они будут переданы в метод sendMsgImpl.
     */
    public final function sendMsg(PsUser $author, PsUser $receiver, $param1 = null, $param2 = null) {
        $ctxt = new TemplateMessageCtxt($author, $receiver, $this->getFoldedEntity()->getDbCode());
        $arguments = func_get_args();
        array_shift($arguments);
        //Убираем из аргументов $author и ставим $ctxt вместо $receiver
        $arguments[0] = $ctxt;
        call_user_func_array(array($this, 'sendMsgImpl'), $arguments);
    }

    /**
     * Функция, отправляющая сообщение от имени системного администратора.
     * Нужна для отправки всяких уведомлений.
     */
    public final function sendSystemMsg(PsUser $receiver, $param1 = null, $param2 = null) {
        $arguments = func_get_args();
        array_unshift($arguments, PsUser::defaultAdmin());
        call_user_func_array(array($this, 'sendMsg'), $arguments);
    }

    /**
     * Основная функция, занимающаяся декодированием дискуссионного сообщения.
     * 
     * @return TemplateMessageContent
     */
    public final function decodeMsg(DiscussionMsg $msg) {
        check_condition($msg->getTemplateId() === $this->getFoldedEntity()->getDbCode(), "Класс $this не может декодировать $msg");
        return $this->decodeMsgImpl($msg);
    }

}

?>