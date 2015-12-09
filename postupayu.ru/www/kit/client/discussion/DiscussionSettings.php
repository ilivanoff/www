<?php

/**
 * Настройки для дерева дискуссии. Вынесены в отдельный класс, чтобы не плодить 
 * множество protected методов или заставлять наследовать разные интерфейсы.
 *
 * @author azazello
 */
final class DiscussionSettings {

    private $group;
    private $subgroup;
    private $unique;
    private $table;
    private $idColumn;
    private $threadIdColumn;
    private $themeColumn;
    private $msgClass;
    private $templatable;
    private $votable;

    /**
     * @param type $group - группа дискуссий (должна быть уникальна среди всех групп, например - посты или фидбеки)
     * @param type $subgroup - подгруппа дискуссии (должна быть уникальна в рамках группы)
     * @param type $unique - уникальный код дискуссии, комбинация группы и подгруппы
     * @param type $table - таблица с сообщениями
     * @param type $idColumn - первичный ключ таблицы с сообщениями
     * @param type $threadIdColumn - столбец с кодом треда (для дискуссий, работающих с тредами)
     * @param type $themeColumn - столбец с темой (если идёт работа с темой)
     * @param type $msgClass - клсс, хранящий сообщение. Должен быть наследником DiscussionMsg.
     * @param type $templatable - признак, можно ли использовать шаблонные сообщения
     */
    public function __construct($group, $subgroup, $table, $idColumn, $msgClass = 'DiscussionMsg', $threadIdColumn = null, $themeColumn = null, $templatable = false, $votable = true) {
        $this->group = $group;
        $this->subgroup = $subgroup;
        $this->unique = $group . ($subgroup ? "-$subgroup" : '');
        $this->table = $table;
        $this->idColumn = $idColumn;
        $this->msgClass = $msgClass;
        $this->threadIdColumn = $threadIdColumn;
        $this->themeColumn = $themeColumn;
        $this->templatable = $templatable;
        $this->votable = $votable;

        check_condition(PsUtil::isInstanceOf($msgClass, 'DiscussionMsg'), "Класс '$msgClass' не наследует DiscussionMsg");
    }

    public function getGroup() {
        return $this->group;
    }

    public function getSubgroup() {
        return $this->subgroup;
    }

    public function getUnique() {
        return $this->unique;
    }

    public function getThreadUnique($threadId) {
        return 'thread-' . $this->unique . '-' . $threadId;
    }

    public function getMsgUnique($msgId) {
        return 'msg-' . $this->unique . '-' . $msgId;
    }

    public function getTable() {
        return $this->table;
    }

    public function getIdColumn() {
        return $this->idColumn;
    }

    public function getThreadIdColumn() {
        return $this->threadIdColumn;
    }

    public function getThemeColumn() {
        return $this->themeColumn;
    }

    public function getMsgClass() {
        return $this->msgClass;
    }

    public function isThemed() {
        return !!$this->themeColumn;
    }

    public function isWorkWithThreadId() {
        return !!$this->threadIdColumn;
    }

    public function isTemplatable() {
        return $this->templatable;
    }

    public function isVotable() {
        return $this->votable;
    }

}

?>