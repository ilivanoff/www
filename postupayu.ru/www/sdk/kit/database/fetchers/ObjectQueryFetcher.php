<?php

/**
 * Description of ObjectQueryFetcher
 *
 * @author azazello
 */
class ObjectQueryFetcher extends ArrayQueryFetcher {
    /**
     * Типы фетчинга
     */

    const FETCH_TYPE_ARRAY = 1; //Объекты извлекаются в массив. Если передана $idxCol, то эта колонка будет использована, как индекс.
    const FETCH_TYPE_RECORD = 2; //Обязательно извлекается один объект
    const FETCH_TYPE_RECORD_OR_NULL = 3; //Извлекается один объект или null

    /** @var ReflectionClass */

    private $RC;

    /** Колонка индексирования, если требуется индексировать */
    private $idxCol;

    /** Параметры для конструктора объекта $class */
    private $constructorParams;

    /** Тип фетчинга */
    private $fetchType;

    /** Кастомный `фетчер` */
    private $fetcher;

    /** Признак использования кастомного `фетчера` */
    private $useFetcher;

    protected function __construct($class, $idxCol, array $constructorParams = null, $fetchType = self::FETCH_TYPE_ARRAY, callable $fetcher = null) {
        parent::__construct($idxCol, false);
        $this->RC = PsUtil::newReflectionClass($class);
        $this->idxCol = $idxCol;
        $this->constructorParams = $constructorParams;
        $this->fetchType = $fetchType;
        $this->fetcher = $fetcher;
        $this->useFetcher = is_callable($fetcher);
    }

    /**
     * Метод формирует экземпляр класса на основе строки.
     * После будет применён фильтр, если он есть.
     * 
     * @return object
     */
    private function getInst(array $row) {
        if ($this->constructorParams) {
            $args = $this->constructorParams;
            $args[] = $row;
            return $this->RC->newInstanceArgs($args);
        } else {
            return $this->RC->newInstance($row);
        }
    }

    public function fetchResult(array $ROWS) {
        switch ($this->fetchType) {

            /*
             * Фетчим в массив
             */
            case self::FETCH_TYPE_ARRAY:
                $idxCol = $this->idxCol;
                $result = array();
                foreach ($ROWS as $row) {
                    if ($idxCol) {
                        $idx = $row[$idxCol];
                        if ($this->filterKey($idx)) {
                            $inst = $this->getInst($row);
                            if ($inst) {
                                if ($this->useFetcher) {
                                    //http://stackoverflow.com/questions/3637164/why-does-the-error-expected-to-be-a-reference-value-given-appear
                                    $params = array();
                                    $params[] = $inst;
                                    $params[] = &$result;
                                    $params[] = $row;
                                    call_user_func_array($this->fetcher, $params);
                                } else {
                                    $result[$idx] = $inst;
                                }
                            }
                        }
                    } else {
                        $inst = $this->getInst($row);
                        if ($inst) {
                            if ($this->useFetcher) {
                                $params = array();
                                $params[] = $inst;
                                $params[] = &$result;
                                $params[] = $row;
                                call_user_func_array($this->fetcher, $params);
                            } else {
                                $result[] = $inst;
                            }
                        }
                    }
                }
                return $result;

            /*
             * Фетчим одну запись
             */
            case self::FETCH_TYPE_RECORD:
            case self::FETCH_TYPE_RECORD_OR_NULL:
                $rowsCnt = count($ROWS);
                switch ($rowsCnt) {
                    case 0:
                        if ($this->fetchType == self::FETCH_TYPE_RECORD_OR_NULL) {
                            return null; //--
                        }
                        return PsUtil::raise('Данные для объекта [{}] не найдены', $this->RC->getName());

                    case 1:
                        $inst = $this->getInst($ROWS[0]);
                        if ($inst) {
                            return $inst;
                        }
                        return PsUtil::raise('Объект [{}] загружен, но не прошёл фильтрацию', $this->RC->getName());
                }
                return PsUtil::raise('Найдено {} записей при извлечении объекта [{}]', $rowsCnt, $this->RC->getName());
        }

        PsUtil::raise('Неизвестный тип фетчинга {} для {}', $this->fetchType, __CLASS__);
    }

    /** @return ObjectQueryFetcher */
    public static function inst($class, $idxCol = null, array $constructorParams = null, $fetchType = self::FETCH_TYPE_ARRAY, callable $fetcher = null) {
        return new ObjectQueryFetcher($class, $idxCol, $constructorParams, $fetchType, $fetcher);
    }

}

?>