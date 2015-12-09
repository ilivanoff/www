<?php

/**
 * Основной класс для работы с БД.
 */
final class PSDB {

    /** @return ADORecordSet */
    private static function executeQuery($query, $params = false, &$queryFinal = null, array &$paramsFinal = null) {
        $queryFinal = $query instanceof Query ? $query->build($params) : $query;
        $queryFinal = normalize_string($queryFinal);

        $paramsFinal = to_array($params);

        $LOGGER = PsLogger::inst(__CLASS__);

        $PROFILER = PsProfiler::inst(__CLASS__);

        $PROFILER->start(strtolower($queryFinal));
        try {
            if ($LOGGER->isEnabled()) {
                $LOGGER->info("[$queryFinal]" . ($paramsFinal ? ', PARAMS: ' . array_to_string($paramsFinal) : ''));
            }
            $rs = PsConnectionPool::conn()->execute($queryFinal, $paramsFinal);
            if (is_object($rs)) {
                $PROFILER->stop();
                return $rs;
            }
            $error = PsConnectionPool::conn()->ErrorMsg();

            $LOGGER->info('ERROR: {}', $error);

            throw new DBException($error, DBException::ERROR_NOT_CLASSIFIED, $queryFinal, $paramsFinal);
        } catch (Exception $ex) {
            $PROFILER->stop(false);
            if ($ex instanceof DBException) {
                ExceptionHandler::dumpError($ex);
            }
            throw $ex;
        }
    }

    /**
     * Возвращает индексированный массив ассоциативных массивов со строками БД.
     */
    public static function getArray($query, $inputarr = false, ArrayQueryFetcher $fetcher = null) {
        return $fetcher ? $fetcher->fetchResult(self::executeQuery($query, $inputarr)->GetArray()) : self::executeQuery($query, $inputarr)->GetArray();
    }

    /**
     * Метод предназначен для загрузки единичной записи. Если извлекается более одной записи -
     * выбразывается исключение.
     *
     * $ensureHasOne:
     * true - если не найдена единственная запись, выкидывает ошибку
     * false - если не найдена единственная запись, метод возвращает null
     */
    public static function getRec($query, $inputarr = false, $ensureHasOne = false) {
        $rs = self::executeQuery($query, $inputarr, $queryFinal, $paramsFinal);
        $rs->Close();

        switch ($rs->RecordCount()) {
            case 0:
                if ($ensureHasOne) {
                    throw new DBException('No data found', DBException::ERROR_NO_DATA_FOUND, $queryFinal, $paramsFinal);
                }
                return null; //---

            case 1:
                return $rs->fields; //---

            default:
                throw new DBException('Too many rows', DBException::ERROR_TOO_MANY_ROWS, $queryFinal, $paramsFinal);
        }

        PsUtil::raise('Unexpected recs count requrned: {}', $rs->RecordCount());
    }

    /**
     * Метод выполняет апдейт записи в базе
     */
    public static function update($query, $inputarr = false) {
        self::executeQuery($query, $inputarr);
        return PsConnectionPool::conn()->Affected_Rows();
    }

    /**
     * Делает тоже самое, что и update, только возвращает последний айдишник.
     * Это приводит к выполнению 'SELECT LAST_INSERT_ID()', поэтому лишний раз
     * лучше не вызывать.
     */
    public static function insert($query, $inputarr = false) {
        self::executeQuery($query, $inputarr);
        return PsConnectionPool::conn()->Insert_ID();
    }

    /**
     * Возвращает строку для данной таблицы, заполненной дефолтными значениями.
     * Можно передать устанавливаемые значения в $cols4replace самостоятельно.
     */
    public static function getEmptyRec($table, array $cols4replace = array()) {
        $rec = array();
        /* @var $value ADOFieldObject */
        foreach (PsConnectionPool::conn()->MetaColumns($table) as $name => $value) {
            $name = strtolower($name);
            $rec[$name] = array_get_value($name, $cols4replace, $value->has_default ? $value->default_value : null);
        }
        return $rec;
    }

}

?>