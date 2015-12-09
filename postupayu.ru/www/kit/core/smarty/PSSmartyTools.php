<?php

class PSSmartyTools {

    private static $TYPES = array(Smarty::PLUGIN_BLOCK, Smarty::PLUGIN_FUNCTION, Smarty::PLUGIN_MODIFIER);

    /**
     * smarty_block_answers или block_answers -> block
     */
    public static function getFunctionType($__FUNCTION__) {
        foreach (self::$TYPES as $type) {
            if (starts_with($__FUNCTION__, $type . '_')) {
                return $type;
            }
            if (starts_with($__FUNCTION__, 'smarty_' . $type . '_')) {
                return $type;
            }
        }
        check_condition(false, "Bad smarty function [$__FUNCTION__]");
    }

    /**
     * smarty_block_answers или block_answers -> answers
     */
    public static function getFunctionName($__FUNCTION__) {
        foreach (self::$TYPES as $type) {
            if (starts_with($__FUNCTION__, $type . '_')) {
                return cut_string_start($__FUNCTION__, $type . '_');
            }
            if (starts_with($__FUNCTION__, 'smarty_' . $type . '_')) {
                return cut_string_start($__FUNCTION__, 'smarty_' . $type . '_');
            }
        }
        check_condition(false, "Bad smarty function [$__FUNCTION__]");
    }

    public static function checkFunctionType($type) {
        check_condition(in_array($type, self::$TYPES), "Illegal smarty function type [$type].");
    }

}

?>
