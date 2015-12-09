<?php

/**
 * Контекст для фетчинга информационных шаблонов фолдингов.
 * Вынесен в отдельный класс, так как при фетчинге обынчых шаблонов могут 
 * потребоваться данные из информационных.
 *
 * @author azazello
 */
class FoldedInfoTplContext extends FoldedContext {

    /** @return FoldedEntity */
    public function getFoldedEntity() {
        return $this->getContext();
    }

    public function setContextWithFoldedEntity(FoldedEntity $entity) {
        $this->setContext($entity->getUnique(), $entity);
    }

    /** @return FoldedContext */
    public static function getInstance() {
        return parent::inst();
    }

}

?>