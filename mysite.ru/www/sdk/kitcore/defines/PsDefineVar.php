<?php

/**
 * Глобаьные переменные системы
 *
 * @author azazello
 */
final class PsDefineVar extends PsEnum {

    /** @return PsDefineVar */
    public static final function REPLACE_FORMULES_WITH_IMG() {
        return self::inst(PsDefines::TYPE_D);
    }

    /** @return PsDefineVar */
    public static final function REPLACE_FORMULES_WITH_SPRITES() {
        return self::inst(PsDefines::TYPE_D);
    }

    /** @return PsDefineVar */
    public static final function LOGGING_ENABLED() {
        return self::inst(PsDefines::TYPE_GD);
    }

    /** @return PsDefineVar */
    public static final function LOGGING_STREAM() {
        return self::inst(PsDefines::TYPE_G);
    }

    /** @return PsDefineVar */
    public static final function LOGGERS_LIST() {
        return self::inst(PsDefines::TYPE_G);
    }

    /** @return PsDefineVar */
    public static final function PROFILING_ENABLED() {
        return self::inst(PsDefines::TYPE_GD);
    }

    /** @return PsDefineVar */
    public static final function NORMALIZE_PAGE() {
        return self::inst(PsDefines::TYPE_D);
    }

    /** @return PsDefineVar */
    public static final function TABLE_DUMP_PORTION() {
        return self::inst(PsDefines::TYPE_D);
    }

    private $type;

    protected function init($type = null) {
        PsDefines::validateVar($this->name(), $type);
        $this->type = $type;
    }

    public function has() {
        return PsDefines::has($this->name(), $this->type);
    }

    public function set($val) {
        PsDefines::set($this->name(), $val, $this->type);
    }

    public function get($default = null) {
        return PsDefines::get($this->name(), $this->type, $default);
    }

}

?>