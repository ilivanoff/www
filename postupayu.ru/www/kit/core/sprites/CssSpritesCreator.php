<?php

final class CssSpritesCreator {

    /** @var CssSpriteGen */
    private $CssSpriteGen;

    /** @var PsLoggerInterface */
    private $LOGGER;

    /** @var PsProfilerInterface */
    private $PROFILER;

    public function CreateSprite(CssSprite $sprite) {
        if ($sprite->exists()) {
            return; //--- Спрайт существует
        }

        $this->LOGGER->info("Waiting to make $sprite");
        PsLock::lockMethod(__CLASS__, __FUNCTION__);
        try {
            if ($sprite->exists()) {
                $this->LOGGER->info('Sprite was created in another thread, skipping');
            } else {
                $this->PROFILER->start('Sprite creation');
                $this->CssSpriteGen->CreateSprite($sprite);
                $this->LOGGER->info('Sprite was successfully created, path: ' . $sprite->getCssDi()->getAbsPath());
                $this->PROFILER->stop();
            }
        } catch (Exception $ex) {
            PsLock::unlock();
            throw $ex;
        }
        PsLock::unlock();
    }

    /*
     * СИНГЛТОН
     */

    private static $INSTS = array();

    /** @return CssSpritesCreator */
    public static function inst(array $params = array()) {
        $hash = simple_hash($params);
        return (self::$INSTS[$hash] = array_key_exists($hash, self::$INSTS) ? self::$INSTS[$hash] : new CssSpritesCreator($params));
    }

    private function __construct(array $params) {
        ExternalPluginsManager::SpriteGenerator();
        $this->CssSpriteGen = new CssSpriteGen($params);
        $this->LOGGER = PsLogger::inst(__CLASS__);
        $this->PROFILER = PsProfiler::inst(__CLASS__);
    }

}

?>