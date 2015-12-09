<?php

/**
 * Маппинг одних сущностей на другие.
 * Хэш-код маппинга складывается из хэш кодов источников.
 *
 * @author azazello
 */
final class Mapping implements MappingClient {

    /** @var MappingSource */
    private $LSRC;

    /** @var MappingSource */
    private $RSRC;

    /** Уникальный хэш-код маппинга */
    private $MHASH;

    /** Описание маппинга */
    private $DESCR;

    /*
     * Синглтон
     */
    private static $insts = array();

    /**
     * Метод получения экземпляра маппинга
     * @return MappingClient
     */
    public static final function inst(MappingSource $lsrc, MappingSource $rsrc, $descr) {
        $hash = md5($lsrc->getHash() . '|' . $rsrc->getHash());
        if (!array_key_exists($hash, self::$insts)) {
            check_condition($lsrc->getMident() == $rsrc->getMident(), "Источники не совместимы: $lsrc <> $rsrc");
            self::$insts[$hash] = new Mapping($lsrc, $rsrc, $hash, $descr);
        }
        return self::$insts[$hash];
    }

    private function __construct(MappingSource $srcLeft, MappingSource $srcRight, $hash, $descr) {
        $this->LSRC = $srcLeft;
        $this->RSRC = $srcRight;
        $this->MHASH = $hash;
        $this->DESCR = $descr;
    }

    /**
     * Уникальный hash-код маппинга
     */
    public function getHash() {
        return $this->MHASH;
    }

    /**
     * Идентификатор маппинга. В рамках одного идентификатора источники данных 
     * могут вести себя по разному. У LSRC и RSRC идентификаторы совпадают.
     */
    public function getMident() {
        return $this->LSRC->getMident();
    }

    /**
     * Описание маппинга.
     * В основном для отображения в админке.
     */
    public function getDescription() {
        return $this->DESCR;
    }

    /**
     * Описание истоника данных слева.
     * В основном для отображения в админке.
     */
    public function getDescriptionLsrc() {
        return $this->LSRC->getDescription();
    }

    /**
     * Описание источника данных справа.
     * В основном для отображения в админке.
     */
    public function getDescriptionRsrc() {
        return $this->RSRC->getDescription();
    }

    /**
     * Возвращает список сущностей, привязанных к идентификатору слева, 
     * не проверяя их реального существования.
     */
    public function getMappedEntitysUnsafe($lident) {
        return MappingBean::inst()->getMappedEntitysDb($this->MHASH, $lident);
    }

    /**
     * Возвращает список сущностей, привязанных к идентификатору слева,
     * проверяя существование самих сущностей.
     * 
     * Если в базу попадёт запись о привязке к сущности, которой нет в системе -
     * она будет отсечена путём пересечения списка привязанных сущностей к 
     * сущностям, возвращаемых загрузчиком.
     */
    public function getMappedEntitys($lident) {
        return array_intersect($this->getMappedEntitysUnsafe($lident), $this->getIdentsRight($lident));
    }

    /**
     * Метод вовзращает карту соответствия для каждой из сущности слева.
     * Если соответствия нет, элемент в массив добавлен не будет.
     */
    public function getAllMappedEntitys(array $lidents) {
        $result = array();
        foreach ($lidents as $lident) {
            $ridents = $this->getMappedEntitys($lident);
            if ($ridents) {
                $result[$lident] = $ridents;
            }
        }
        return $result;
    }

    /**
     * Возвращает список идентификаторв слева
     */
    public function getIdentsLeft() {
        return $this->LSRC->getIdentsLeft();
    }

    /**
     * Возвращает список идентификаторв права для сущности слева
     */
    public function getIdentsRight($lident) {
        return $this->RSRC->getIdentsRight($this->LSRC, $lident);
    }

    /**
     * Приведение к строке
     */
    public function __toString() {
        return __CLASS__ . "[{$this}]: " . $this->LSRC . ' -> ' . $this->RSRC;
    }

}

?>