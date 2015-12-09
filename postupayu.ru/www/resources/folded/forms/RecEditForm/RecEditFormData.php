<?php

class RecEditFormData implements FormSuccess {

    /** @var PsTable */
    private $table;
    private $rec;
    private $action;
    private $isProcessFolding;

    function __construct(PsTable $table, $action, $isProcessFolding, array $rec) {
        $this->table = $table;
        $this->rec = $rec;
        $this->action = $action;
        $this->isProcessFolding = $isProcessFolding;
    }

    /** @return PsTable */
    public function getTable() {
        return $this->table;
    }

    public function getAction() {
        return $this->action;
    }

    /**
     * Создаваемая/Изменяемая/Удаляемая запись
     */
    public function getRec() {
        return $this->rec;
    }

    /**
     * Признак - выполнять ли действие над фолдингом вместе с действием над записью
     */
    public function isProcessFolding() {
        return $this->isProcessFolding;
    }

}

?>