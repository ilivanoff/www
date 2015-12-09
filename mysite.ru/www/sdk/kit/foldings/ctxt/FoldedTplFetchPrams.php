<?php

/**
 * Базовый класс для всех классов, хранящих параметры фолдинга.
 * 
 * После фетчинга шаблона его содержимое передаётся в контекст, в котоом он был отфетчен, для финализации.
 * В результате финализации доожен быть возвращён массив, содержащий все ключи, объявленные в этом классе
 * в виде констант с префиксом PARAM_.
 *
 * @author azazello
 */
class FoldedTplFetchPrams extends BaseDataStore {

    const PARAM_CONTENT = 'content';

    public final function __construct(array $data) {
        parent::__construct($data);
    }

    public static function getClassName() {
        return get_called_class();
    }

    /**
     * Получение содержимого отфетченного шаблона фолдинга.
     * Если у фолдинга бали запрошены только ресурсы, то содержимого может и не быть...
     */
    public function getContent() {
        check_condition($this->hasKey(self::PARAM_CONTENT), 'Cannot get tpl content, only data is available.');
        return parent::__get(self::PARAM_CONTENT);
    }

}

?>