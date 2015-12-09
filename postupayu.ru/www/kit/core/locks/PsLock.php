<?php

/**
 * Меденжер блокировок.
 * 
 * Если мы не сохраняем ссылку на заблокированный файл, то
 * он будет закрыт автоматически и, соответственно, автоматически будет отпущена 
 * блокировка.
 * 
 * Пример использования:

  PsLock::lockMethod(__CLASS__, __FUNCTION__);
  try {
  ...
  } catch (Exception $ex) {
  PsLock::unlock();
  throw $ex;
  }
  PsLock::unlock();
 */
final class PsLock extends AbstractSingleton implements Destructable {

    public static function lock($lockname, $wait = true) {
        return self::inst()->doLock($lockname, $wait);
    }

    public static function lockMethod($__CLASS__, $__FUNCTION__, $wait = true) {
        return self::inst()->doLock($__CLASS__ . '::' . $__FUNCTION__, $wait);
    }

    public static function unlock() {
        self::inst()->doUnlock();
    }

    /** @var PsLoggerInterface */
    private $LOGGER;

    /** Параметры локов */
    private $lockCnt = 0;
    private $lockFile = null;
    private $lockName = null;

    /**
     * Метод выполняет фактическое получение лока
     */
    private function doLock($lockname, $wait) {
        if ($this->lockName == $lockname) {
            $this->lockCnt++;
            $this->LOGGER->info('Lock ident [{}] counter inreased to {}.', $lockname, $this->lockCnt);
            return true;
        }

        check_condition($lockname, 'Lock ident cannot be empty');
        check_condition(!$this->lockName, "Lock [$lockname] cannot be set, previous lock [{$this->lockName}] is active");

        $filename = md5($lockname);

        $this->LOGGER->info("Trying to get lock with ident [$lockname], mode: {}. Lock file name=[$filename].", $wait ? 'WAIT' : 'NOWAIT');

        /**
         * Храним в stuff, а не в autogen, так как можем потерять локи при удалении autogen
         * или вообще не иметь возможности удалить папку autogen.
         */
        $di = DirManager::stuff(null, 'locks')->getDirItem(null, $filename, 'lock');

        /*
         * Файл будет создан при открытии
         */
        $fp = fopen($di->getAbsPath(), 'a+');

        do {
            $this->LOGGER->info('Locking file...');
            if (flock($fp, $wait ? LOCK_EX : LOCK_EX | LOCK_NB)) {
                $this->lockCnt = 1;
                $this->lockFile = $fp;
                $this->lockName = $lockname;

                $this->LOGGER->info('Lock acquired!');

                return true;
            }

            //Мы не получили блокировку...
            if ($wait) {
                $this->LOGGER->info('Lock not acquired, sleep for 1 sec');
                sleep(1);
            }
        } while ($wait);

        @fclose($fp);

        $this->LOGGER->info("Lock not setted.\n");
        return false;
    }

    /**
     * Метод отпускает полученную ранее блокировку.
     */
    private function doUnlock() {
        if ($this->lockCnt > 1) {
            $this->lockCnt--;
            $this->LOGGER->info('Lock ident [{}] counter decreased to {}.', $this->lockName, $this->lockCnt);
            return; //---
        }
        if ($this->lockCnt == 1) {
            $this->lockCnt--;

            flock($this->lockFile, LOCK_UN);
            fclose($this->lockFile);

            $this->LOGGER->info("Lock [{$this->lockName}] released!\n");

            $this->lockFile = null;
            $this->lockName = null;
        }
    }

    /**
     * Убедимся, что блокировка была отпущена
     */
    public function onDestruct() {
        $this->lockCnt = $this->lockCnt > 1 ? 1 : $this->lockCnt;
        $this->doUnlock();
    }

    /** @return PsLock */
    protected static function inst() {
        return parent::inst();
    }

    protected function __construct() {
        $this->LOGGER = PsLogger::inst(__CLASS__);
        PsShotdownSdk::registerDestructable($this, PsShotdownSdk::PsLock);
    }

}

?>