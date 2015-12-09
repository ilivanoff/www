<?php

/**
 * Description of IniFilesTestHelpef
 *
 * @author azazello
 */
class IniFilesTestHelper {

    /**
     * Получением DirItem на ini файлы
     */
    public static function di($num) {
        return DirItem::inst(__DIR__, 'ini' . $num, 'ini');
    }

    public static function arr($num, $process_sections = true) {
        return parse_ini_file(DirItem::inst(__DIR__, 'ini' . $num, 'ini')->getAbsPath(), $process_sections);
    }

}

?>