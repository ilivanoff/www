<?php

/**
 * Сущность фолдинга - связка фолдинга и сущности, входящей в него.
 * Используется в разных частных случаях, например при разворачиваниии фолдинга из архива.
 *
 * @author azazello
 */
class FoldedEntity implements Spritable {

    private $ident;
    private $folding;

    private function __construct(FoldedResources $folding, $ident) {
        $this->folding = $folding;
        $this->ident = $ident;
    }

    private static $items = array();

    /** @return FoldedEntity */
    public static function inst(FoldedResources $folding, $ident, $assertExists = true) {
        $unique = $folding->getUnique($ident);
        if (!array_key_exists($unique, self::$items)) {
            if ($assertExists) {
                $folding->assertExistsEntity($ident);
            }
            self::$items[$unique] = new FoldedEntity($folding, $ident);
        }
        return self::$items[$unique];
    }

    public function getIdent() {
        return $this->ident;
    }

    /**
     * @return FoldedResources 
     */
    public function getFolding() {
        return $this->folding;
    }

    /**
     * Обложка жлемента
     * 
     * @return DirItem
     */
    public function getCover($dim = null) {
        return $this->folding->getCover($this->ident, $dim);
    }

    /**
     * Уникальный идентификатор сущности фолдинга
     * @param type $elIdent - идентфиикатор сущности, относящейся к этой сущности фолдинга. Например - формула или картинка в шаблоне
     */
    public function getUnique($elIdent = null) {
        return $this->folding->getUnique($this->ident) . ($elIdent ? "-$elIdent" : '');
    }

    /** @return DirManager */
    public function getResourcesDm($subDir = null) {
        return $this->folding->getResourcesDm($this->ident, $subDir);
    }

    public function getResourcesLinks($content = null) {
        return $this->folding->getResourcesLinks($this->ident, $content);
    }

    public function fetchTpl(array $smartyParams = array(), $returnType = FoldedResources::FETCH_RETURN_CONTENT, $addResources = false, $cacheId = null) {
        return $this->folding->fetchTplImpl($this->ident, $smartyParams, $returnType, $addResources, $cacheId);
    }

    public function fetchTplWithResources($smartyParams = null) {
        return $this->folding->fetchTplWithResources($this->ident, $smartyParams);
    }

    public function getInfo($tplPath, array $smartyParams = array()) {
        return $this->folding->getInfo($this->ident, $tplPath, $smartyParams);
    }

    public function setDependsOnEntity(FoldedEntity $parent) {
        $this->folding->setDependsOnEntity($this->ident, $parent);
    }

    public function onEntityChanged() {
        $this->folding->onEntityChanged($this->ident);
    }

    /** @return FoldedClass */
    public function getClassInst() {
        return $this->folding->getEntityClassInst($this->ident);
    }

    public function getDbCode() {
        return FoldingBean::inst()->getEntityCode($this);
    }

    public function __toString() {
        return $this->folding->getTextDescr($this->ident);
    }

    public function equalTo($entity) {
        return self::equals($this, $entity);
    }

    public static function equals($ob1, $ob2) {
        if (!($ob1 instanceof FoldedEntity) || !($ob2 instanceof FoldedEntity)) {
            return false;
        }
        return $ob1->getUnique() === $ob2->getUnique();
    }

    /** @return CssSprite */
    public function getSprite() {
        return $this->folding->getSprite($this->ident);
    }

    public function getSpriteName() {
        return $this->folding->getSpriteName($this->ident);
    }

    public function getSpriteImages() {
        return $this->folding->getSpriteImages($this->ident);
    }

}

?>