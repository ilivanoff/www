<?php

/**
 * Базовый класс для всех сообщений, отображаемых в дереве
 *
 * @author azazello
 */
class DiscussionMsg extends BaseDataStore {

    /** @var DiscussionSettings */
    protected $SETTINGS;

    public function getId() {
        return (int) parent::__get($this->SETTINGS->getIdColumn());
    }

    public function getParentId() {
        return (int) $this->id_parent;
    }

    public function getRootId() {
        return $this->id_root;
    }

    public function getThreadId() {
        return $this->SETTINGS->isWorkWithThreadId() ? (int) parent::__get($this->SETTINGS->getThreadIdColumn()) : null;
    }

    public function isRoot() {
        return isEmpty($this->id_parent);
    }

    public function isChild() {
        return !$this->isRoot();
    }

    public function getContent() {
        return $this->isDeleted() ? null : $this->content;
    }

    public function getTheme() {
        return $this->SETTINGS->isThemed() && !$this->isDeleted() && $this->isRoot() ? parent::__get($this->SETTINGS->getThemeColumn()) : null;
    }

    public function getDeep() {
        return $this->n_deep;
    }

    public function isMaxDeepLevel() {
        return $this->getDeep() >= MAX_COMMENTS_DEEP_LEVEL;
    }

    public function isDeleted() {
        return $this->b_deleted;
    }

    public function isKnown() {
        return $this->b_deleted || $this->b_known || !$this->id_user_to || ($this->id_user_to == $this->id_user);
    }

    public function isConfirmed() {
        return $this->b_confirmed;
    }

    /** @return PsUser */
    public function getUser() {
        return PsUser::inst($this->id_user);
    }

    /** @return PsUser */
    public function getUserTo() {
        return is_numeric($this->id_user_to) ? PsUser::inst($this->id_user_to) : null;
    }

    public function isToUser($user) {
        return $this->getUserTo() && $this->getUserTo()->isIt($user);
    }

    public function getDtEvent($format = DF_COMMENTS) {
        return DatesTools::inst()->uts2dateInCurTZ($this->dt_event, $format);
    }

    private $childs = array();

    public function addChild(DiscussionMsg $msg) {
        check_condition(!array_key_exists($msg->getId(), $this->childs), 'Cicle!');
        $this->childs[$msg->getId()] = $msg;
    }

    public function getChilds() {
        return $this->childs;
    }

    public function hasChilds() {
        return !empty($this->childs);
    }

    public function isTemplated() {
        return $this->SETTINGS->isTemplatable() && is_numeric($this->id_template);
    }

    public function getTemplateId() {
        return $this->isTemplated() ? (int) $this->id_template : null;
    }

    public function getTemplateData() {
        return $this->isTemplated() ? $this->v_template : null;
    }

    public function getUnique() {
        return $this->SETTINGS->getMsgUnique($this->getId());
    }

    public final function __construct(DiscussionSettings $settings, array $data) {
        parent::__construct($data);
        $this->SETTINGS = $settings;
        /*
         * Обратимся к пользователю, чтобы потом, в процессе обращения к аватару
         * пользователя, все они были загружены батчем.
         */
        $this->getUser();
    }

    public function __toString() {
        return get_called_class() . ' [' . $this->SETTINGS->getIdColumn() . '=' . $this->getId() . ']';
    }

}

?>