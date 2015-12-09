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

//Массив для сохранения информации о картинках
$images = array();

//Создадим $OUT_DIR
$dm->makePath($OUT_DIR);

$outputDir = $dm->absDirPath($OUT_DIR);

foreach ($items as $item) {
    $name = $item->getNameNoExt();

    $srcImg = SimpleImage::inst()->load($item);

    $w = $srcImg->getWidth();
    $h = $srcImg->getHeight();

    $outImg = SimpleImage::inst()->create($w, $h, null);

    for ($x = 0; $x < $w; $x++) {
        for ($y = 0; $y < $h; $y++) {
            $rgb = $srcImg->colorAt($x, $y);
            if ($rgb != 16777215 && $rgb != 255) {
                $outImg->copyFromAnother($srcImg, $x, $y, $x, $y, 1, 1);
            }
        }
    }

    $filename = file_path($outputDir, $name, 'png');
    $outImg->save($filename, IMAGETYPE_PNG)->close();
    $srcImg->close();

    $images[] = array(
        'rel' => file_path($OUT_DIR, $name, 'png'),
        'rels' => file_path($SRC_DIR, $item->getName()),
        'name' => $name);
}

saveResult2Html('pngs.tpl', array('items' => $images), __DIR__);
?>
