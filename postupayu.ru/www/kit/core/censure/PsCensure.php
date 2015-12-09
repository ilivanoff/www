<?php

/**
 * Менеджер отпределяет нецензурную лексику в тексте
 *
 * @author azazello
 */
class PsCensure {

    public static function parse($string) {
        ExternalPluginsManager::Censure();
        return $string ? Text_Censure::parse($string, '2', "\xe2\x80\xa6", false) : false;
    }

}

?>