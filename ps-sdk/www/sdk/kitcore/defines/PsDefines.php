<?php

/**
 * Расширение {@see PsDefinesEngine}
 * 
 * @author azazello
 */
final class PsDefines extends PsDefinesEngine {
    /*
     * ЗАМЕНА ФОРМУЛ НА КАРТИНКИ
     */

    const F_REPLACE_NONE = 'F_REPLACE_NONE'; // - Замена не производится
    const F_REPLACE_IMG = 'F_REPLACE_IMG'; //   - Заменяем на картинки
    const F_REPLACE_SPRITES = 'F_REPLACE_SPRITES'; // - Заменяем на спрайты

    /** @return PsDefines */

    public static function setReplaceFormulesWithImages($replace) {
        PsDefineVar::REPLACE_FORMULES_WITH_IMG()->set($replace);
    }

    public static function isReplaceFormulesWithImages() {
        return PsDefineVar::REPLACE_FORMULES_WITH_IMG()->get();
    }

    /** @return PsDefines */
    public static function setReplaceFormulesWithSprites($replace) {
        PsDefineVar::REPLACE_FORMULES_WITH_SPRITES()->set($replace);
    }

    public static function isReplaceFormulesWithSprites() {
        return PsDefineVar::REPLACE_FORMULES_WITH_SPRITES()->get();
    }

    //Тип замены формул на картинки
    public static function getReplaceFormulesType() {
        if (self::isReplaceFormulesWithImages()) {
            return self::isReplaceFormulesWithSprites() ? self::F_REPLACE_SPRITES : self::F_REPLACE_IMG;
        }
        return self::F_REPLACE_NONE;
    }

    /*
     * ЛОГИРОВАНИЕ
     */

    public static function isLoggingEnabled() {
        return PsDefineVar::LOGGING_ENABLED()->get();
    }

    public static function getLoggingStream($default) {
        return PsDefineVar::LOGGING_STREAM()->get($default);
    }

    public static function getLoggersList() {
        return PsDefineVar::LOGGERS_LIST()->get();
    }

    /*
     * ПРОФИЛИРОВАНИЕ
     */

    public static function isProfilingEnabled() {
        return PsDefineVar::PROFILING_ENABLED()->get();
    }

    /*
     * АУДИТ
     */

    public static function getTableDumpPortion() {
        return PsDefineVar::TABLE_DUMP_PORTION()->get();
    }

    /*
     * РЕЖИМ PRODUCTION
     */

    public static function isProduction() {
        return PS_PRODUCTION;
    }

    public static function isDevmode() {
        return !self::isProduction();
    }

    public static function assertProductionOff($class) {
        check_condition(!self::isProduction(), "'$class' is not available in production mode.");
    }

    /*
     * ПРОВОДИТЬ ЛИ НОРМАЛИЗАЦИЮ СТРАНИЦЫ
     */

    public static function isNormalizePage() {
        return PsDefineVar::NORMALIZE_PAGE()->get();
    }

    public static function setNormalizePage($normalize) {
        return PsDefineVar::NORMALIZE_PAGE()->set($normalize);
    }

}

?>