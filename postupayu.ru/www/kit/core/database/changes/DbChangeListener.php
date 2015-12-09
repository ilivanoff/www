<?php

/**
 * Класс следит за изменениями БД и оповещает слушателей.
 */
final class DbChangeListener extends AbstractSingleton {

    /**
     * Раз уж есдинственная задача данного класса - проверить изменения данных в БД,
     * то мы все действия можем выполнить в конструкторе, воспользовавшись тем
     * обстоятельством, что мы - синглтон.
     * 
     * Важно! Получение экземпляра нужно вызывать с $silentOnDoubleTry=true, так как
     * при выполнении действий в конструкторе данного класса мы можем прийти к фолдингу,
     * который вызовет DbChangeListener::check().
     */
    public static function check() {
        parent::inst(true);
    }

    protected function __construct() {
        //Извлекаем все изменённые сущности БД
        $CHANGED = DbBean::inst()->getChangedEntitys();

        if (empty($CHANGED)) {
            return; //---
        }

        $LOGGER = PsLogger::inst(__CLASS__);
        $LOGGER->info('Изменённые сущности БД: ' . print_r($CHANGED, true));

        foreach ($CHANGED as $chEntity) {
            $type = $chEntity['v_type'];
            $entity = $chEntity['v_entity'];
            $TypeEntity = "$type $entity";
            switch ($type) {
                /*
                 * 1. Проверим изменённые сущности фолдинга
                 */
                case DbBean::CHANGE_FOLD_ENT:
                    $fentity = Handlers::getInstance()->getFoldedEntityByUnique($entity, false);
                    if ($fentity) {
                        $LOGGER->info('[{}] -> Сущность фолдинга [{}]', $TypeEntity, $fentity->getUnique());
                        FoldedResourcesManager::onEntityAction(FoldedResourcesManager::ACTION_ENTITY_CHANGED_DB, $fentity->getFolding(), $fentity->getIdent());
                        $fentity->onEntityChanged();
                    }
                    break;

                /*
                 * 2. Проверим изменённые таблицы/представления
                 */
                case DbBean::CHANGE_TABLE:
                case DbBean::CHANGE_VIEW:
                    /* @var $folding FoldedResources */
                    foreach (FoldedResourcesManager::inst()->getTableOrViewFoldings($entity) as $folding) {
                        $LOGGER->info('[{}] -> Фолдинг [{}]', $TypeEntity, $folding->getUnique());
                        $folding->onFoldingChanged();
                    }

                    foreach (PSCache::inst()->onDbEntityChanged($entity) as $cacheGr) {
                        $LOGGER->info('[{}] -> Группа кеширования [{}]', $TypeEntity, $cacheGr);
                    }

                    break;
            }
        }
    }

}

?>