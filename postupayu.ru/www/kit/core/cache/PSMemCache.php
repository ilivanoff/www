<?php

/**
 * Description of PSMemCache
 *
 * @author azazello
 */
final class PSMemCache extends AbstractSingleton {

    /** @var PsLoggerInterface */
    private $LOGGER;

    /** @var Memcache */
    private $MEMCACHE = null;

    /** @return Memcache */
    public static function inst() {
        return parent::inst()->MEMCACHE;
    }

    /** @return bool */
    public static function enabled() {
        return is_object(self::inst());
    }

    protected final function __construct() {
        $this->LOGGER = PsLogger::inst(__CLASS__);

        $class = 'Memcache';

        if (!class_exists($class)) {
            $this->LOGGER->info($class . ' class is not exists!');
            return; //---
        }

        $this->MEMCACHE = new $class;

        if (!@$this->MEMCACHE->connect('127.0.0.1', 11211)) {
            $this->LOGGER->info('Could not connect to localhost!');
            $this->MEMCACHE = null;
            return; //---
        }

        $this->LOGGER->info($class . ' is enabled.');
    }

}

?>