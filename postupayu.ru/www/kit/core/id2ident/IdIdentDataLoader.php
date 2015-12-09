<?php

abstract class IdIdentDataLoader {
    /*
     * Для переопределения
     */

    protected abstract function entityTitle();

    protected abstract function loadEntitysLiteDB();

    protected abstract function loadEntitysFullDB(array $ids, $loadAll);

    private $ENTITYS;
    private $ENTITY_IDENT2ID;

    private function initImpl($returnEntity) {
        $this->ENTITYS = array();
        $this->ENTITY_IDENT2ID = array();

        /* @var $entity IdIdentInerfase */
        foreach ($this->loadEntitysLiteDB() as $entity) {
            $id = $entity->getId();
            $ident = $entity->getIdent();
            $this->ENTITYS[$id] = $entity;
            $this->ENTITY_IDENT2ID[$ident] = $id;
        }

        return $returnEntity ? $this->ENTITYS : $this->ENTITY_IDENT2ID;
    }

    public function getEntitys() {
        return isset($this->ENTITYS) ? $this->ENTITYS : $this->initImpl(true);
    }

    private function getId2IdentMap() {
        return isset($this->ENTITY_IDENT2ID) ? $this->ENTITY_IDENT2ID : $this->initImpl(false);
    }

    public function hasWithId($id) {
        return array_key_exists($id, $this->getEntitys());
    }

    public function hasWithIdent($ident) {
        return array_key_exists($ident, $this->getId2IdentMap());
    }

    public function assertExistsWithId($id, $raise = true) {
        check_condition(!$raise || $this->hasWithId($id), $this->entityTitle() . " с кодом [$id] не существует.");
    }

    public function assertExistsWithIdent($ident, $raise = true) {
        check_condition(!$raise || $this->hasWithIdent($ident), $this->entityTitle() . " с идентификатором [$ident] не существует.");
    }

    public function getById($id, $ensure = false) {
        $this->assertExistsWithId($id, $ensure);
        return array_get_value($id, $this->getEntitys());
    }

    public function getByIdent($ident, $ensure = false) {
        $this->assertExistsWithIdent($ident, $ensure);
        return array_get_value($this->getIdByIdent($ident), $this->getEntitys());
    }

    public function getIdByIdent($ident, $ensure = false) {
        $this->assertExistsWithIdent($ident, $ensure);
        return array_get_value($ident, $this->getId2IdentMap());
    }

    public function extractEntity($IdOrIdent, $ensure = false) {
        if (!$IdOrIdent && !$ensure) {
            //Убрать этот блок нельзя, так как может возникнуть зацикливание при вызове entityTitle
            return null;
        }

        $byId = is_numeric($IdOrIdent) ? $this->getById($IdOrIdent) : null;
        $byIdent = is_string($IdOrIdent) ? $this->getByIdent($IdOrIdent) : null;

        if (!$byId && !$byIdent) {
            check_condition(!$ensure, 'Не удалось определить сущность ' . $this->entityTitle());
            return null;
        }

        if ($byId && $byIdent) {
            check_condition($byId->getId() == $byIdent->getId(), "Cущность {$this->entityTitle()} найдена по id и по ident: [$IdOrIdent]");
            return $byId;
        }

        return $byId ? $byId : $byIdent;
    }

    public function getCount() {
        return count($this->getEntitys());
    }

    public function hasItems() {
        return $this->getCount() > 0;
    }

    /*
     * Полный вариант сущностей
     */

    private $CONTENTS = array();

    private function isContentLoaded($id) {
        return array_key_exists($id, $this->CONTENTS);
    }

    public function registerContents($contents) {
        $this->getEntitys();

        $contents = to_array($contents);
        foreach ($contents as $entity) {
            if (is_array($entity)) {
                $this->registerContents($entity);
                continue;
            }

            if (!is_object($entity)) {
                continue;
            }

            if (!method_exists($entity, 'getId') || !method_exists($entity, 'getIdent')) {
                check_condition(false, "Контент сущности {$this->entityTitle()} не имеет методов getId и getIdent");
            }

            $id = $entity->getId();
            $this->assertExistsWithId($id);

            $this->ENTITYS[$id] = $entity;
            $this->CONTENTS[$id] = true;
        }
    }

    public function getContentsByIds($ids) {
        $ids = to_array($ids);

        $ids4load = array();
        foreach ($ids as $id) {
            if ($this->hasWithId($id) && !$this->isContentLoaded($id) && !in_array($id, $ids4load)) {
                $ids4load[] = $id;
            }
        }

        if (!empty($ids4load)) {
            $this->registerContents($this->loadEntitysFullDB($ids4load, count($ids4load) === $this->getCount()));
        }

        $snapshot = array();
        foreach ($ids as $id) {
            if ($this->isContentLoaded($id)) {
                $snapshot[$id] = $this->getById($id);
            }
        }
        return $snapshot;
    }

    public function getContentById($id, $ensure = false) {
        $this->assertExistsWithId($id, $ensure);
        return array_get_value($id, $this->getContentsByIds($id));
    }

    public function getContentByIdent($ident, $ensure = false) {
        $this->assertExistsWithIdent($ident, $ensure);
        return $this->getContentById($this->getIdByIdent($ident));
    }

    public function preloadAllContents() {
        $this->getContentsByIds(array_keys($this->getEntitys()));
    }

}

?>
