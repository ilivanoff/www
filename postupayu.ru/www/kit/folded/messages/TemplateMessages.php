<?php

/**
 * Description of TemplateMessages
 *
 * @author azazello
 */
final class TemplateMessages extends FoldedResources {

    /** Допустимые типы ресурсов */
    protected $RESOURCE_TYPES_ALLOWED = array(self::RTYPE_PHP, self::RTYPE_TPL);

    protected function isIncludeToList($ident, $list) {
        return false;
    }

    protected function onEntityChangedImpl($ident) {
        
    }

    public function getEntityName() {
        return 'Шаблонное сообщение';
    }

    public function getFoldingType() {
        return 'msg';
    }

    public function getFoldingSubType() {
        return null;
    }

    public function getFoldingGroup() {
        return 'messages';
    }

    /**
     * Метод декодирования шаблонных сообщений
     * 
     * @return TemplateMessageContent
     */
    public function decodeTemplateMsg(DiscussionMsg $msg) {
        try {
            check_condition($msg->isTemplated(), "Сообщение $msg не шаблонизировано");
            $result = $this->getFoldedEntityByDbCode($msg->getTemplateId())->getClassInst()->decodeMsg($msg);
            if ($result instanceof TemplateMessageContent) {
                return $result;
            }
            raise_error(is_string($result) && !isEmpty($result) ? $result : 'Шаблонное сообщение обработано некорректно');
        } catch (Exception $ex) {
            return new TemplateMessageError($ex);
        }
    }

    /** @return TemplateMessages */
    public static function inst() {
        return parent::inst();
    }

}

?>