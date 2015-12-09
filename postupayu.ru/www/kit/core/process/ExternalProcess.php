<?php

/**
 * Процесс, который должен периодически запускаться.
 * Пока мы его реализуем, как вызов от имени пользователя при запросе страницы.
 * В будущем можно вынести на cron или на службу проверки доступности сайтов.
 * Но вообще, конечно, нужно иметь ввиду: http://habrahabr.ru/post/179399/
 */
final class ExternalProcess extends AbstractSingleton {

    /** Признак - была ли попытка вызова. Обработку выполняем всего 1 раз за время выполнения скрипта. */
    private $called = false;

    /** Признак - выполнился ли процесс фактически. */
    private $executed = false;

    /**
     * Вызов от имени клиента (одни из n)
     */
    public function executeFromClient() {
        //Не будем выполнять процесс а ajax контексте, так как ajax должен быстро отрабатывать
        if (PageContext::inst()->isAjax()) {
            return $this->executed;
        }
        return $this->executeImpl(EXTERNAL_PROCESS_CALL_DELAY, __FUNCTION__);
    }

    /**
     * Выполнение от имени процесса, зыпускаемого периодически.
     * Интервал вызова настраивается в самом процессе, но при этом мы всёже этот интервал ограничим,
     * чтобы защититься от перегрузки сервера.
     */
    public function executeFromProcess() {
        return $this->executeImpl(floor(EXTERNAL_PROCESS_CALL_DELAY / 4), __FUNCTION__);
    }

    private function executeImpl($lifeTimeOnCall, $__FUNCTION__) {
        if ($this->called) {
            return $this->executed; //---
        }
        $this->called = true;

        $LOGGER = PsLogger::inst(__CLASS__);

        $LOGGER->info("Function [$__FUNCTION__] called.");

        $needProcess = false;
        $LOCKFILE = DirManager::autogen('service')->getDirItem(null, __CLASS__, 'lock');
        if ($LOCKFILE->isFile()) {
            $lifeTime = $LOCKFILE->getFileLifetime();
            $needProcess = !$lifeTime || !$lifeTimeOnCall || ($lifeTime >= $lifeTimeOnCall);
            $LOGGER->info('{} process. Lock file modified {} seconds ago. Process delay: {} seconds.', $needProcess ? 'Need' : 'Not need', $lifeTime, $lifeTimeOnCall);
        } else {
            $needProcess = true;
            $LOGGER->info('Need process. Lock file is not exists.');
        }

        if (!$needProcess) {
            return $this->executed; //---
        }

        $locked = PsLock::lock(__CLASS__, false);
        $LOGGER->info('External process lock {} execution.', $locked ? 'acquired, start' : 'not acquired, skip');
        if ($locked) {
            $LOCKFILE->touch();
            PsUtil::startUnlimitedMode();

            //Отпустим лок, так как внутри он может потребоваться для выполнения других действий, например для перестройки спрайтов
            PsLock::unlock();

            //Начинаем выполнение
            $this->executed = true;

            $job = new ExternalProcessJob();

            PsProfiler::inst(__CLASS__)->start(__FUNCTION__);
            $job->execute();
            $secundomer = PsProfiler::inst(__CLASS__)->stop();

            if ($secundomer) {
                $LOGGER->info("$secundomer");
            }
        }

        return $this->executed;
    }

    /** @return ExternalProcess */
    public static function inst() {
        return parent::inst();
    }

}

?>