<?php

/**
 * Хранилище всех сиквенсов
 *
 * @author azazello
 */
final class PsSequence {

    /**
     * Сиквенс для логгера
     * 
     * @return AbstractSequence
     */
    public static function LOG() {
        return PsSequenceFile::inst(PsLogger::DM()->getDirItem(null, 'lastnum'), 1, PsLogger::MAX_SESSIONS);
    }

}

?>