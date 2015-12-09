<?php

/**
 * Ошибка в шаблонном сообщении - оно отсутствует или было некорректно обработано
 *
 * @author azazello
 */
class TemplateMessageError extends TemplateMessageContent {

    public function __construct(Exception $ex) {
        //parent::__construct(PsHtml::spanErr('Ошибка шаблонного сообщения'), ExceptionHandler::inst()->getHtml($ex));
        parent::__construct(PsHtml::spanErr('Ошибка отображения сообщения'), PsHtml::divErr($ex->getMessage()));
    }

}

?>
