<?php

/**
 * Сервис для выполнения дампа таблиц
 *
 * @author azazello
 */
final class AdminTableDump {

    const DUMPS_TABLE = 'db-dumps';

    /**
     * Метод выполняет дамп таблицы
     */
    public static function dumpTable($idColumn, $table, array $where = array(), $order = null) {
        //Стартуем секундомер
        $secundomer = Secundomer::startedInst("Снятие дампа $table");

        //Отключим ограничение по времени
        PsUtil::startUnlimitedMode();

        //Получим экземпляр логгера
        $LOGGER = PsLogger::inst(__CLASS__);

        //Текущий пользователь
        $userId = AuthManager::getUserIdOrNull();

        //Для логов, запросов и все остального
        $table = strtoupper(PsCheck::tableName($table));

        //Макс кол-во записей
        $limit = PsDefines::getTableDumpPortion();

        //Проверим наличие id
        $idColumn = PsCheck::tableColName($idColumn);

        $LOGGER->info('Dumping table {}. Id column: {}, limit: {}. User id: {}.', $table, $idColumn, $limit, $userId);

        //Получаем лок (без ожидания)
        $lockName = "DUMP table $table";
        $locked = PsLock::lock($lockName, false);
        $LOGGER->info('Lock name: {}, locked ? {}.', $lockName, var_export($locked, true));

        if (!$locked) {
            return false; //Не удалось получить лок
        }

        $zipDi = false;

        try {
            //ЗПРОСЫ:
            //1. Запрос на извлечение колва записей, подлежащих дампированию
            $queryCnt = Query::select("count($idColumn) as cnt", $table, $where);
            //2. Запрос на извлечение данных, подлежащих дампированию
            $queryDump = Query::select('*', $table, $where, null, $order, $limit);
            //3. Запрос на извлечение кодов дампируемых записей
            $selectIds = Query::select($idColumn, $table, $where, null, $order, $limit);
            //4. Запрос на удаление дампированных данных
            $queryDel = Query::delete($table, Query::plainParam("$idColumn in (select $idColumn from (" . $selectIds->build($delParams) . ') t )', $delParams));

            //Выполним запрос для получения кол-ва записей, подлежащих дампу
            $cnt = PsCheck::int(array_get_value('cnt', PSDB::getRec($queryCnt, null, true)));
            $LOGGER->info('Dump recs count allowed: {}.', $cnt);

            if ($cnt < $limit) {
                $LOGGER->info('SKIP dumping table, count allowed ({}) < limit ({})...', $cnt, $limit);
                $LOGGER->info("Query for extract dump records count: $queryCnt");
                PsLock::unlock($lockName);
                return false;
            }

            //Время дампа
            $date = PsUtil::fileUniqueTime(false);
            $time = time();

            //Название файлов
            $zipName = $date . ' ' . $table;

            //Элемент, указывающий на zip архив
            $zipDi = DirManager::stuff(null, array(self::DUMPS_TABLE, $table))->getDirItem(null, $zipName, PsConst::EXT_ZIP);

            $LOGGER->info('Dump to: [{}].', $zipDi->getAbsPath());
            if ($zipDi->isFile()) {
                $LOGGER->info('Dump file exists, skip dumping table...');
                PsLock::unlock($lockName);
                return false;
            }

            //Комментарий к таблице
            $commentToken[] = "Date: $date";
            $commentToken[] = "Time: $time";
            $commentToken[] = "Table: $table";
            $commentToken[] = "Manager: $userId";
            $commentToken[] = "Recs dumped: $limit";
            $commentToken[] = "Total allowed: $cnt";
            $commentToken[] = "Query dump cnt:   $queryCnt";
            $commentToken[] = "Query dump data:  $queryDump";
            $commentToken[] = "Query dump ids:   $selectIds";
            $commentToken[] = "Query del dumped: $queryDel";

            $comment = implode("\n", $commentToken);

            //Начинаем zip и сохраняем в него данные
            $zip = $zipDi->startZip();
            $zip->addFromString($zipName, serialize(PSDB::getArray($queryDump)));
            $zip->setArchiveComment($comment);
            $zip->close();

            $LOGGER->info('Data successfully dumped, zip comment:');
            $LOGGER->info("[\n$comment\n]");

            //Удалим те записи, дамп которых был снят
            $LOGGER->info('Clearing dumped table records...');
            $affected = PSDB::update($queryDel);
            $LOGGER->info('Rows deleted: {}.', $affected);
            $LOGGER->info('Dumping is SUCCESSFULLY finished. Total time: {} sec.', $secundomer->stop()->getAverage());
        } catch (Exception $ex) {
            PsLock::unlock($lockName);
            ExceptionHandler::dumpError($ex);
            $LOGGER->info('Error occured: {}', $ex->getMessage());
            throw $ex;
        }

        return $zipDi;
    }

    /**
     * Метод загрузки информации по всем дампам указанной таблицы
     */
    public static function getAllDumpsInfo($table) {
        $items = DirManager::stuff(self::DUMPS_TABLE)->getDirContent(strtoupper($table), PsConst::EXT_ZIP);
        $RESULT = array();
        /* @var $item DirItem */
        foreach ($items as $item) {
            $TOKEN = array();
            $TOKEN['abs'] = $item->getAbsPath();
            $TOKEN['rel'] = $item->getRelPath();
            $TOKEN['name'] = $item->getName();
            $TOKEN['size'] = $item->getSize();
            $zip = $item->loadZip();
            $TOKEN['comment'] = $zip->getArchiveComment();
            $zip->close();

            $RESULT[] = $TOKEN;
        }
        return $RESULT;
    }

}

?>