<?php

/**
 * Информационный шаблон - шаблон, относящийся к сущности фолдинга и содержащий информацию о ней
 *
 * @author azazello
 */
class FoldedInfoTpl {

    private $tpl;
    private $entity;

    private function __construct(FoldedEntity $entity, DirItem $tplDi) {
        $this->tpl = $tplDi;
        $this->entity = $entity;
    }

    /**
     * @return FoldedInfoTpl
     */
    private static $INSTS = array();

    public static function inst(FoldedEntity $entity, DirItem $tplDi) {
        if (!array_key_exists($tplDi->getRelPath(), self::$INSTS)) {
            self::$INSTS[$tplDi->getRelPath()] = new FoldedInfoTpl($entity, $tplDi);
        }
        return self::$INSTS[$tplDi->getRelPath()];
    }

    /**
     * Элемент файловой системы, соответствующий шаблону
     * 
     * @return DirItem
     */
    public function getDirItem() {
        return $this->tpl;
    }

    /**
     * Сущность, к которой относится данный шаблон
     * 
     * @return FoldedEntity
     */
    public function getFoldedEntity() {
        return $this->entity;
    }

    /**
     * Возвращает путь для информационного шаблона относительно папки информационных шаблонов:
     * /resources/folded/stocks/mosaic/tpl/stock1.tpl -> /stock1.tpl
     */
    public function getInfoRelPath() {
        return $this->entity->getFolding()->getInfoTplRelPath($this);
    }

    /**
     * Фетчинг шаблона с заданными параметрами в контексте, относящемся к данной сущности фолдинга
     */
    public function fetch(array $smartyParams = array()) {
        return $this->entity->getFolding()->getInfo($this->entity->getIdent(), $this->tpl, $smartyParams);
    }

    /**
     * Фетчинг шаблона без кеширования, как есть
     */
    public function fetchNoCache(array $smartyParams = array()) {
        return $this->entity->getFolding()->getInfoTplCtt($this->entity->getIdent(), $this->tpl, $smartyParams);
    }

}

?>
