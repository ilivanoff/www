<?php

/**
 * Интерфейс, скрывающий функционал, относящийся к конкретному коду и предоставляющий 
 * методы для работы со всеми кодами.
 * 
 * @author azazello
 */
interface PsUserCodeController {

    public function dropUnusedCodes($userId);

    /** @return PsUserCode */
    public function generateAndSave($userId);
}

?>
