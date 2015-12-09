<?php

final class IdHelper {

    public static function ident($item) {
        if ($item instanceof AbstractPost) {
            return self::postId($item->getPostType(), $item->getIdent());
        }
        if ($item instanceof Rubric) {
            return self::rubricId($item->getPostType(), $item->getIdent());
        }

        raise_error('В IdHelper передан элемент неподдрживаемого типа: ' . PsUtil::getClassName($item));
    }

    /*
     * 1.1 -> f1X1
     */

    public static function formulaId($num) {
        return 'f' . str_replace('.', 'X', $num);
    }

    /*
     * 1.1 -> i1X1
     */

    public static function blockImgId($imageId) {
        return 'i' . str_replace('.', 'X', $imageId);
    }

    /**
     * Пример решения задачи
     * 
     * 1.1 -> e1X1
     */
    public static function exId($num) {
        return 'e' . str_replace('.', 'X', $num);
    }

    /**
     * Пример решения задачи
     * 
     * 1.1 -> th1X1
     */
    public static function thId($num) {
        return 'th' . str_replace('.', 'X', $num);
    }

    /**
     * Local href - местная ссылка
     * id -> lid
     */
    public static function localId($id) {
        return "l$id";
    }

    /**
     * Гимнастическое упражнение
     * id -> gym_id
     */
    public static function gymExId($id) {
        return "gymex_$id";
    }

    /*
     * Комментарий, пример: comment_bp_1
     */

    public static function msgId($unique, $commentId) {
        return 'msg_' . $unique . '_' . $commentId;
    }

    /*
     * Пост, пример: post_tr_kinemtochki
     */

    public static function postId($type, $ident) {
        return 'post_' . $type . '_' . $ident;
    }

    /*
     * Пост, пример: rubric_tr_phys
     */

    public static function rubricId($type, $ident) {
        return 'rubric_' . $type . '_' . $ident;
    }

}

?>