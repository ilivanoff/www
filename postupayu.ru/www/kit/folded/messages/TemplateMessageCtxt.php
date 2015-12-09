<?php

/**
 * Контекст отправки письма пользователю
 *
 * @author azazello
 */
class TemplateMessageCtxt {

    /** @var PsUser */
    private $author;

    /** @var PsUser */
    private $receiver;

    /** Код шаблонного сообщения */
    private $templateId;

    public function __construct(PsUser $author, PsUser $receiver, $templateId) {
        $this->author = $author;
        $this->receiver = $receiver;
        $this->templateId = $templateId;
    }

    public function getAuthor() {
        return $this->author;
    }

    public function getReceiver() {
        return $this->receiver;
    }

    public function send($templateData = null) {
        FeedbackManager::inst()->saveTemplatedMessage($this->receiver->getId(), null, $this->templateId, $templateData, $this->author);
    }

}

?>
