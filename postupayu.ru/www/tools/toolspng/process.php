<?php

/*
 * Выполняет обработку.
 */

require_once '../ToolsResources.php';
$CALLED_FILE = __FILE__;

$SRC_DIR = 'source';
$OUT_DIR = 'output';

$dm = DirManager::inst(__DIR__);

//Создадим $SRC_DIR
$dm->makePath($SRC_DIR);

//Перенесём все картинки из корня в $SRC_DIR
$items = $dm->getDirContent(null, DirItemFilter::IMAGES);
foreach ($items as $img) {
    copy($img->getAbsPath(), $dm->absFilePath($SRC_DIR, $img->getName()));
    $img->remove();
}

//Удалим из $SRC_DIR всё, кроме *.bat и *.php
$items = $dm->getDirContent(null, DirItemFilter::FILES);
foreach ($items as $file) {
    if (!$file->checkExtension(array('bat', 'php'))) {
        $file->remove();
    }
}

//Очистим $OUT_DIR
$items = $dm->getDirContent($OUT_DIR, DirItemFilter::IMAGES);
foreach ($items as $img) {
    $img->remove();
}

$items = $dm->getDirContent($SRC_DIR, DirItemFilter::IMAGES);

if (isEmpty($items)) {
    return; //---
}

//Создадим $OUT_DIR
$dm->makePath($OUT_DIR);

$outputDir = $dm->absDirPath($OUT_DIR);

foreach ($items as $item) {
    $name = $item->getNameNoExt();
    SimpleImage::inst()->load($item)->resizeSmart(36, 36)->save($dm->absFilePath($OUT_DIR, $name, 'png'), IMAGETYPE_PNG)->close();
}
?>
