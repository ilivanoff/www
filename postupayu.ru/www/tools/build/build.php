<?php

require_once dirname(__DIR__) . '/ToolsResources.php';
$CALLED_FILE = __FILE__;

/*
 * Удаляем страницы, которые не используются в PagesManager
 */
LOGBOX('Excluding pages');
$pages = WebPages::allowedScripts();
//Добавим форсированно
$pages[] = 'MainImport.php';
$pages[] = 'MainImportAdmin.php';

$rootPages = DirManager::inst()->getDirContent(null, PsConst::EXT_PHP);
/** @var DirItem */
foreach ($rootPages as $page) {
    $name = $page->getName();
    if (in_array($name, $pages)) {
        dolog("+ $name");
    } else {
        dolog("- $name");
        $page->remove();
    }
}



/*
 * УСТАНОВИМ КОНСТАНТЫ
 */
LOGBOX('Set consts');
$props = DirItem::inst(__DIR__, 'consts.txt')->getFileAsProps();
dolog(print_r($props, true));
PsGlobals::inst()->updateProps($props);



/*
 * УДАЛИМ ТЕСТОВЫЕ ДИРЕКТОРИИ
 */
LOGBOX('Remove test dirs');
$dirs = DirManager::inst()->getDirContentFull(null, DirItemFilter::DIRS);
$testDirNames = array(
    'temp',
    'orig',
    '4use',
    //'test',
    'tests',
    'testcase'
);

$testDirStartsWith = array(
    '!'
);

/* @var $dir DirItem */
foreach ($dirs as $dir) {
    if (in_array($dir->getName(), $testDirNames) || starts_with($dir->getName(), $testDirStartsWith)) {
        $path = $dir->getAbsPath();
        dolog('Removing: ' . $path);
        DirManager::inst($path)->clearDir(null, true);
    }
}



/*
 * УДАЛИМ АРХИВЫ
 */
LOGBOX('Remove archivs');
$archivs = DirManager::inst()->getDirContentFull(null, DirItemFilter::ARCHIVES);

/* @var $dir DirItem */
foreach ($archivs as $arc) {
    dolog('Removing: ' . $arc->getAbsPath());
    $arc->remove();
}


dolog("\nFile actions successfully finished\n");
?>