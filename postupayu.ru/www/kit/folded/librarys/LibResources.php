<?php

/**
 * В этот класс для удобства вынесено всё, что "знает" информацию о библиотечных сущностях
 */
abstract class LibResources extends FoldedResources implements TimeLineFolding, BubbledFolding, DatabasedFolding, ImagedFolding {

    protected $RESOURCE_TYPES_ALLOWED = array();

    const LIB_FOLDING_TYPE = 'lib';

    /**
     * getFoldingType - одинаков для всех библиотек
     * getFoldingSubType - будет использован для работы с базой.
     */
    public function getFoldingType() {
        return self::LIB_FOLDING_TYPE;
    }

    /**
     * Метод возвращает из базы сущности данной библиотеки,
     * хранимые для которых есть фолдинги на файловой системе.
     */
    private function getLibItemsDb() {
        return $this->getVisibleDbObjects(LibItemDb::getClass());
    }

    /** @return LibItemDb */
    public function getLibItemDb($ident, $assert = true) {
        $item = $ident ? array_get_value($ident, $this->getLibItemsDb()) : null;
        check_condition(!$assert || ($item instanceof LibItemDb), "Элемент [{$this->getEntityName()}] с идентификатором [$ident] не существует.");
        return $item;
    }

    /**
     * @return FoldedEntity
     */
    public function getTLBuilderFoldedEntity() {
        return TimeLineManager::inst()->getFoldedEntity4TimeLineFolding($this);
    }

    /**
     * Метод конвертирует запись в базе для сущности библиотеки в элемент хронологической шкалы.
     * Если элемент не должен отображаться на временной шкале 
     * (не задано начало события или метод фильтрации исключил данное событие), вернётся null.
     * 
     * @return TimeLineItem
     */
    private function convertLibItemDb2TimeLineItem(LibItemDb $libItem, ArrayAdapter $params) {
        if (!$libItem->getDtStart()) {
            //Если нет даты начала - событие не стоит брать
            return null;
        }
        $tlItem = TimeLineItem::inst($libItem->getName(), $libItem->getIdent(), $libItem->getDtStart(), $libItem->getDtStop());
        //Обложка
        $tlItem->setImage($this->getCover($libItem->getIdent(), TimeLineManager::COVERS_DIM));
        //Контент
        $tlItem->setContent($libItem->getContent());
        //Заполним данными
        if ($this->fillTimeLineItem($libItem, $tlItem, $params)) {
            return $tlItem;
        }
        return null;
    }

    /**
     * Сущности временной шкалы.
     * По умолчанию каждая библиотека может отображать свои сущности на временнОй шкале.
     * Если у события не указана дата начала, то он не будет показан на временной шкале.
     * 
     * @return TimeLineItemsComposite
     */
    public function buildTimeLineComposition(ArrayAdapter $params) {
        $store = new TimeLineItemsComposite();

        /* @var $libItem LibItemDb */
        foreach ($this->getLibItemsDb() as $libItem) {
            $store->addItem($this->convertLibItemDb2TimeLineItem($libItem, $params));
        }

        return $store->colorOneByOne();
    }

    /**
     * Строит представление элемента библиотеке в хронологической шкале.
     */
    public function buildTimeLineItemPresentation($ident, ArrayAdapter $params) {
        return $this->timeLineItemPresentation($this->getLibItemDb($ident), $params);
    }

    /**
     * Метод должен вернуть полное представление для отображаемой сущности временной шкалы
     */
    protected abstract function timeLineItemPresentation(LibItemDb $item, ArrayAdapter $params);

    /**
     * Метод можно переопределить для самостоятельного заполнения элемента временной шкалы
     * 
     * @param LibItemDb $libItem
     * @param TimeLineItem $tlItem
     * @return boolean - если будет возвращён false, событие вообще не будет взято
     */
    protected function fillTimeLineItem(LibItemDb $libItem, TimeLineItem $tlItem, ArrayAdapter $params) {
        //$ident = $libItem->getIdent();
        //$tlItem->setLink('xxx');
        //$tlItem->setColorSchema('xxx');
        //$tlItem->setContent($this->getTpl($ident)->fetch());
        //$tlItem->setContent($libItem->getContent());
        return true;
    }

    //Таблица для хранения сущностей фолдинга
    function foldingTable() {
        return 'v_ps_lib_item.ident.grup';
    }

    //Сущность для поиcка записи в таблице, соответствующей сущности фолдинга
    function dbRec4Entity($ident) {
        return array('grup' => $this->getFoldingSubType(), 'ident' => $ident, 'name' => $ident);
    }

    /**
     * Функции, доступные только администраторам
     */
    public function saveLibItem(LibItemDb $item, AdminLibBean $bean) {
        $this->LOGGER->info("{} lib item: {}", $item->hasId() ? 'Updating' : 'Creating', $item);

        //UPDATE
        if ($item->hasId()) {
            $bean->updateLibItem($item);
            return; //---
        }

        //CREATE
        $id = $bean->createLibItem($item);
        $this->LOGGER->info("Lib item successfully created in DB, id: $id. Trying to create folded entity.");
        try {
            $this->createEntity($item->getIdent());
        } catch (Exception $ex) {
            $this->LOGGER->info("Cannot create entity, reason: {$ex->getMessage()}. Removing LibEntity from DB.");
            $bean->removeLibEntity($id);
            throw $ex;
        }
        $this->LOGGER->info('Lib entity successfully created.');
    }

    /**
     * Возвращает представление для bubble
     */
    public function getBubble($ident) {
        $libItem = $this->getLibItemDb($ident, false);
        if (!$libItem) {
            return null;
        }
        $tlItem = $this->convertLibItemDb2TimeLineItem($libItem, ArrayAdapter::inst());

        $PARAMS['i'] = new LibBubbleItem($libItem, $tlItem);

        return PSSmarty::template('lib/bubble.tpl', $PARAMS)->fetch();
    }

    public function getBubbleHref($ident, $text, ArrayAdapter $params) {
        if (!$ident) {
            return $text;
        }

        $entity = $this->getFoldedEntity($ident);
        $entityDb = $entity ? $this->getLibItemDb($ident, false) : null;
        if ($entity && $entityDb) {
            $text = $text ? $text : $entityDb->getName();
            return PsBubble::spanFoldedEntityBubble($text, $entity->getUnique());
        }

        if ($text) {
            return $text;
        }

        $info = $this->getUnique($ident);
        return PsHtml::spanErr("Не найден библиотечный элемент [$info]");
    }

    public function getFoldedEntityPreview($ident) {
        return array('info' => PsBubble::spanFoldedEntityBubble($ident, $this->getUnique($ident)), 'content' => '');
    }

}

?>