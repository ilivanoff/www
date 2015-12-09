<?php

/**
 * Базовый бин для работы с БД.
 *
 * @author Admin
 */
abstract class BaseBean extends AbstractSingleton {

    /** @var PsLoggerInterface */
    protected $LOGGER;

    /** @var SimpleDataCache */
    protected $CACHE;

    //Проверка, подключены ли мы сейчас к БД
    protected final function isConnected() {
        return PsConnectionPool::isConnectied();
    }

    public final function getCache() {
        return PsDbCahce::getCache(get_called_class());
    }

    protected function getArray($query, $inputarr = false, ArrayQueryFetcher $fetcher = null) {
        return PSDB::getArray($query, $inputarr, $fetcher);
    }

    protected function getRec($query, $inputarr = false) {
        return PSDB::getRec($query, $inputarr);
    }

    protected function getRecEnsure($query, $inputarr = false) {
        return PSDB::getRec($query, $inputarr, true);
    }

    protected function getEmptyRec($table, array $cols4replace = array()) {
        return PSDB::getEmptyRec($table, $cols4replace);
    }

    protected function update($query, $inputarr = false) {
        return PSDB::update($query, $inputarr);
    }

    protected function insert($query, $inputarr = false) {
        return PSDB::insert($query, $inputarr);
    }

    protected function getCnt($query, $inputarr = false) {
        return (int) array_get_value('cnt', $this->getRecEnsure($query, $inputarr));
    }

    protected function getInt($query, $inputarr = false, $default = null) {
        $rec = $this->getRec($query, $inputarr);
        return $rec ? (int) reset($rec) : $default;
    }

    protected function getValue($query, $inputarr = false, $default = null) {
        $rec = $this->getRec($query, $inputarr);
        return $rec ? reset($rec) : $default;
    }

    protected function getArrayIndexed($query, $inputarr, $idxCol) {
        return $this->getArray($query, $inputarr, IndexedArrayQueryFetcher::inst($idxCol));
    }

    protected function getIds($query, $inputarr = false) {
        return $this->getArray($query, $inputarr, IdsQueryFetcher::inst());
    }

    protected function getValues($query, $inputarr = false) {
        return $this->getArray($query, $inputarr, ValuesQueryFetcher::inst());
    }

    protected function getMap($query, $inputarr = false) {
        return $this->getArray($query, $inputarr, MapQueryFetcher::inst());
    }

    protected function getObjects($query, $inputarr, $objName, $idxCol = null, array $constructorParams = null, callable $fetcher = null) {
        return $this->getArray($query, $inputarr, ObjectQueryFetcher::inst($objName, $idxCol, $constructorParams, ObjectQueryFetcher::FETCH_TYPE_ARRAY, $fetcher));
    }

    protected function getObject($query, $inputarr, $objName, array $constructorParams = null, $required = true) {
        return $this->getArray($query, $inputarr, ObjectQueryFetcher::inst($objName, null, $constructorParams, $required ? ObjectQueryFetcher::FETCH_TYPE_RECORD : ObjectQueryFetcher::FETCH_TYPE_RECORD_OR_NULL));
    }

    protected function hasRec($table, $whereAssoc = null) {
        return $this->getCnt(Query::select('count(1) as cnt', $table, $whereAssoc)) > 0;
    }

    protected function __construct() {
        $this->LOGGER = PsLogger::inst(get_called_class());
        $this->CACHE = SimpleDataCache::inst();
    }

}

?>
