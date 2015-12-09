<?php

/**
 * Description of TestSequence
 *
 * @author azazello
 */
class TestSequence {

    public static function fileDi() {
        return DirItem::inst(__DIR__, 'fileSequence');
    }

    public static function fileDiBreak() {
        return DirItem::inst(__DIR__, 'fileSequence2');
    }

}

?>
