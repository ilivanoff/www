<?php

/**
 * Выполнение действий. Можем быть уверены, что:
 * 1. Мы не прервёмся (включён UnlimitedMode)
 * 2. Мы в этот блок вошли одни, больше никто эти действия не выполняет
 */
class ExternalProcessJob {

    public function execute() {
        //1. ПРОВЕРИМ ВСЕ ФОЛДИНГИ, НЕТ ЛИ ИЗМЕНЁННЫХ СУЩНОСТЕЙ
        /* @var $folding FoldedResources */
        foreach (Handlers::getInstance()->getFoldings() as $folding) {
            $folding->checkAllEntitiesChanged();
        }
    }

}

?>