<?php

/**
 * Description of AdminFoldedManager
 *
 * @author azazello
 */
class AdminFoldedManager extends AbstractSingleton {

    /**
     * Метод возвращает все интерфейсы фолдингов
     */
    public function getFoldedInterfaces(array $restriction = null) {
        $files = FoldedResourcesManager::inst()->getFoldedDir()->getDirContentFull("foldinterfaces", PsConst::EXT_PHP);
        $result = array();
        /* @var $file DirItem */
        foreach ($files as $file) {
            $iface = $file->getNameNoExt();
            if (!ends_with($iface, 'Folding')) {
                continue;
            }
            if (!interface_exists($iface)) {
                continue;
            }
            if ($restriction !== null && !in_array($iface, $restriction)) {
                continue;
            }
            $result[$iface] = PhpClassAdapter::inst($iface);
        }
        return $result;
    }

    /**
     * Метод создаёт новый фолдинг
     */
    public function makeNewFolding(ArrayAdapter $adapter) {
        $ifaces = $adapter->arr('ifaces');
        $rtypes = $adapter->arr('rtypes');
        $type = lowertrim($adapter->str('FoldingType'));
        $subtype = lowertrim($adapter->str('FoldingSubType'));
        $group = $adapter->str('FoldingGroup');
        $classPrefix = $adapter->str('FoldingClassPrefix');

        check_condition($type, 'Не передан тип фолдинга');
        check_condition($group, 'Не передана группа для фолдинга');
        check_condition($classPrefix, 'Не передан префикс для классов фолдинга');

        $classesDi = DirItem::inst(array(Autoload::DIR_KIT, 'folded'), $group);
        check_condition(!$classesDi->isDir(), "Директория $classesDi уже существует");
        check_condition(!$classesDi->isFile(), "Элемент $classesDi не может быть файлом");

        $rtypes = array_intersect(array_keys(PsUtil::getClassConsts('FoldedResources', 'RTYPE_')), $rtypes);
        $rtypesArr = trim(implode(', self::', $rtypes));
        $rtypesArr = $rtypesArr ? "self::$rtypesArr" : '';

        $hasPhp = in_array('RTYPE_PHP', $rtypes);

        $classesDm = DirManager::inst($classesDi->getRelPath())->makePath();
        $resourcesPatternDmTo = DirManager::resources(array('folded', $group, FoldedResources::PATTERN_NAME));
        check_condition(!$resourcesPatternDmTo->isDir(), "Целевая директория $resourcesPatternDmTo существует");
        $resourcesPatternDmFrom = DirManager::resources(array('folded', FoldedResources::PATTERN_NAME));
        check_condition($resourcesPatternDmFrom->isDir(), "Некорректна директория-источник $resourcesPatternDmFrom");

        $interfaces = array();
        foreach ($this->getFoldedInterfaces($ifaces) as $name => $ifaceClass) {
            $ctt = $ifaceClass->getClassBody();
            if ($ctt) {
                $interfaces[] = "/****************\n\t * $name\n\t ****************/\n" . $ctt;
            }
        }

        $smParams = $adapter->getData();
        $smParams['rtypes'] = $rtypesArr;
        $smParams['funique'] = FoldedResources::unique($type, $subtype);
        $smParams['BaseClass'] = 'Base' . $classPrefix;
        $smParams['ManagerClass'] = $classPrefix . 'Manager';
        $smParams['ResourcesClass'] = $classPrefix . 'Resources';
        $smParams['implements'] = $ifaces ? 'implements ' . implode(', ', $ifaces) . ' ' : '';
        $smParams['interfaces'] = $interfaces ? implode("\n\n\t", $interfaces) : '';

        try {
            //КЛАССЫ
            //Resources
            $tpl = PSSmarty::template(DirItem::inst(array(__DIR__, 'tpls'), 'resources.tpl'), $smParams);
            $di = $classesDm->getDirItem(null, $smParams['ResourcesClass'], 'php');
            $di->writeToFile(trim($tpl->fetch()), true);

            //Manager
            $tpl = PSSmarty::template(DirItem::inst(array(__DIR__, 'tpls'), 'manager.tpl'), $smParams);
            $di = $classesDm->getDirItem(null, $smParams['ManagerClass'], 'php');
            $di->writeToFile(trim($tpl->fetch()), true);

            if ($hasPhp) {
                //BaseClass
                $tpl = PSSmarty::template(DirItem::inst(array(__DIR__, 'tpls'), 'baseclass.tpl'), $smParams);
                $di = $classesDm->getDirItem(null, $smParams['BaseClass'], 'php');
                $di->writeToFile(trim($tpl->fetch()), true);
            }

            //ШАБЛОН ДЛЯ СУЩНОСТЕЙ
            $resourcesPatternDmTo->makePath();
            foreach ($rtypes as $rtype) {
                $ext = FoldedResources::resourceTypeToExt(PsUtil::newReflectionClass('FoldedResources')->getConstant($rtype));
                $diTo = $resourcesPatternDmTo->getDirItem(null, FoldedResources::PATTERN_NAME, $ext);
                $diFrom = $resourcesPatternDmFrom->getDirItem(null, FoldedResources::PATTERN_NAME, $ext);
                if ($ext == PsConst::EXT_PHP) {
                    $diTo->writeToFile(str_replace('eclassnamebase', $smParams['BaseClass'], $diFrom->getFileContents()));
                    continue;
                }
                $diFrom->copyTo($diTo);
            }
        } catch (Exception $ex) {
            $classesDm->clearDir(null, true);
            $resourcesPatternDmTo->clearDir(null, true);
            throw $ex;
        }
    }

    /** @return AdminFoldedManager  */
    public static function inst() {
        AuthManager::checkAdminAccess();
        return parent::inst();
    }

}

?>