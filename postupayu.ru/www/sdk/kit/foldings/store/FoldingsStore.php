<?php

/**
 * Description of FoldingsStore
 *
 * @author azazello
 */
final class FoldingsStore extends AbstractSingleton {

    /**
     * @var PsLoggerInterface 
     */
    private $LOGGER;

    /**
     * @var PsProfilerInterface 
     */
    private $PROFILER;

    /**
     * Все доступные провайдеры менеджеров фолдингов
     */
    private $PROVIDERS = array();

    /**
     * Привязка фолдинга к провайдеру, из которого он загружен
     */
    private $UNIQUE_2_PROVIDER = array();

    /**
     * Все доступные менеджеры фолдингов, собранные из всех хранилищ
     */
    private $UNIQUE_2_FOLDING = array();

    /**
     * Все доступные менеджеры фолдингов, собранные из всех хранилищ
     */
    private $PROVIDER_2_UNIQUE_2_FOLDING = array();

    /**
     * Метод возвращает полный список зарегистрированных хранилищ
     */
    public function getProviders() {
        return $this->PROVIDERS;
    }

    /**
     * Проверка существования хранилища
     */
    public function hasProvider($provider) {
        return in_array($provider, $this->PROVIDERS);
    }

    /**
     * Метод возвращает все фолдинги из всех доступных хранилищ, относящихся к заданному контексту
     */
    public function getFoldings($scope = ENTITY_SCOPE_ALL) {
        switch ($scope) {
            case ENTITY_SCOPE_ALL:
                return $this->UNIQUE_2_FOLDING;
            case ENTITY_SCOPE_SDK:
            case ENTITY_SCOPE_PROJ:
                if (array_key_exists($scope, $this->PROVIDER_2_UNIQUE_2_FOLDING)) {
                    return $this->PROVIDER_2_UNIQUE_2_FOLDING[$scope];
                }
                $this->PROFILER->start('Foldings[' . $scope . ']');

                $this->PROVIDER_2_UNIQUE_2_FOLDING[$scope] = array();

                $this->LOGGER->info();
                $this->LOGGER->info('Foldings for scope [{}]:', $scope);

                foreach ($this->UNIQUE_2_PROVIDER as $funique => $provider) {
                    if ($provider::isInScope($scope)) {
                        $this->PROVIDER_2_UNIQUE_2_FOLDING[$scope][$funique] = check_condition($this->UNIQUE_2_FOLDING[$funique], "Unknown folding [$funique]");
                        $this->LOGGER->info('[>] {} ({})', $funique, $provider);
                    }
                }

                $this->PROFILER->stop();

                return $this->PROVIDER_2_UNIQUE_2_FOLDING[$scope];
        }
        raise_error("Invalid entity scope [$scope]");
    }

    /**
     * Метод проверяет, относится ли фолдинг к SDK
     */
    public function isSdkFolding(FoldedResources $folding) {
        $funique = $folding->getUnique();
        if (array_key_exists($funique, $this->UNIQUE_2_PROVIDER)) {
            $provider = $this->UNIQUE_2_PROVIDER[$funique];
            return $provider::isSdk();
        }
        raise_error("Folding '$folding' is not registered in any foldings provider");
    }

    /** @return FoldingsStore */
    public static function inst() {
        return parent::inst();
    }

    /**
     * В конструкторе пробежимся по всем хранилищам и соберём все фолдинги
     */
    protected function __construct() {
        $this->LOGGER = PsLogger::inst(__CLASS__);
        $this->PROFILER = PsProfiler::inst(__CLASS__);

        /*
         * Инициалилизируем коллекцию
         */
        $this->PROVIDERS = array();

        /*
         * Собираем полный список доступных хранилищ и менеджеров фолдингов в них
         */
        $providerClasses = ConfigIni::getPropCheckType(ConfigIni::GROUP_FOLDINGS, 'providers', array(PsConst::PHP_TYPE_ARRAY, PsConst::PHP_TYPE_NULL));
        $providerClasses = to_array($providerClasses);
        $providerClasses[] = FoldingsProviderSdk::calledClass();

        $this->LOGGER->info('Providers: {}', array_to_string($providerClasses));

        foreach ($providerClasses as $provider) {
            if (in_array($provider, $this->PROVIDERS)) {
                $this->LOGGER->info('[-] {} - {}', $provider, 'is already registered');
                continue; //---
            }
            if (!class_exists($provider)) {
                $this->LOGGER->info('[-] {} - {}', $provider, 'is not included');
                continue; //---
            }
            if (!PsUtil::isInstanceOf($provider, FoldingsProviderAbstract::calledClass())) {
                $this->LOGGER->info('[-] {} - {}', $provider, 'is not instance of ' . FoldingsProviderAbstract::calledClass());
                continue; //---
            }

            $this->LOGGER->info('[+] {}', $provider);
            $this->PROVIDERS[] = $provider;

            /*
             * Для каждого хранилища загружаем список входящих в него фолдингов
             */
            $this->PROFILER->start($provider . '::list');

            /* @var $folding FoldedResources */
            foreach ($provider::listFoldings() as $folding) {
                $funique = $folding->getUnique();
                if (array_key_exists($funique, $this->UNIQUE_2_PROVIDER)) {
                    raise_error(
                            PsStrings::replaceWithBraced('Folding {} is provided by: {}, {}', $funique, $this->UNIQUE_2_PROVIDER[$funique], $provider)
                    );
                }
                $this->LOGGER->info(' [>] {}', $funique);
                $this->UNIQUE_2_PROVIDER[$funique] = $provider;
                $this->UNIQUE_2_FOLDING[$funique] = $folding;
            }

            $this->PROFILER->stop();
        }
    }

}

?>