<?php

/**
 * Базовый класс для всех сущностей фолдинга {@see TimeLineManager}
 * 
 * @author azazello
 */
abstract class TimeLineBuilderBase extends FoldedClass {

    /** @var PsLoggerInterface */
    protected $LOGGER;

    /** @var TimeLineBuilderParams */
    private $TLPARAMS;

    public function getAuthType() {
        return AuthManager::AUTH_TYPE_NO_MATTER;
    }

    protected function _construct() {
        $this->LOGGER = PsLogger::inst(get_called_class());
        $this->TLPARAMS = $this->getTimeLineBuilderParams();
        $this->TLPARAMS = $this->TLPARAMS ? $this->TLPARAMS : new TimeLineBuilderParams();
    }

    /**
     * Заголовок хроноогической шкалы, который будет отображён на странице всех шкал
     */
    public abstract function getTitle();

    /**
     * Параметры построения хроноогической шкалы, нужны для служебных целей
     * 
     * @return TimeLineBuilderParams
     */
    protected abstract function getTimeLineBuilderParams();

    /**
     * Метод строит и возвращает композицию хронологической шкалы
     * 
     * @return TimeLineItemsComposite
     */
    protected abstract function buildComposition(ArrayAdapter $params);

    /**
     * Метод строит и возвращает представление для элемента хронологической шкалы
     * 
     * @return html
     */
    protected abstract function buildPresentation($ident, ArrayAdapter $params);

    /*
     * УТИЛИТНЫЕ
     */

    /**
     * Метод выкидывает все параметры, которые не ожидаются при построении композиции 
     * хронологической шкалы и на их основе строит ключ кеширования.
     */
    private function prepareBuildCompositionParamsAndGetCahceKey(ArrayAdapter $params) {
        return self::getIdent() . '-' . $params->leaveKeys($this->TLPARAMS->getCompositionExpectedParams())->dataToString();
    }

    /**
     * Метод возвращает композицию хронологической шкалы как массив для json.
     * Прелесть этого метода в том, что он использует кеш.
     * 
     * @return array
     */
    public final function getTimeLineJson(ArrayAdapter $params) {
        $cacheKey = $this->prepareBuildCompositionParamsAndGetCahceKey($params);
        $cache = PSCache::TIMELINES()->getFromCache($cacheKey, array());
        if (is_array($cache)) {
            $this->LOGGER->info('Шкала найдена в кеше под ключём {}', $cacheKey);
            return $cache;
        }
        return $this->getTimeLineComposition($params)->getTimeLineJson();
    }

    /**
     * Метод строит композицию временной шкалы.
     * Построенная композиция будет автоматически сохранена в кеш.
     * Методы были разделены осознанно, возможно нам нужно будет получить именно композицию.
     * 
     * @return TimeLineItemsComposite
     */
    public final function getTimeLineComposition(ArrayAdapter $params) {
        $cacheKey = $this->prepareBuildCompositionParamsAndGetCahceKey($params);
        $this->LOGGER->info('Строим хронологическую шкалу, данные: ' . $cacheKey);
        $this->profilerStart(__FUNCTION__);
        try {
            $composition = $this->buildComposition($params);
            check_condition($composition instanceof TimeLineItemsComposite, 'Некорректно построена хронологическая шкала');
            $this->profilerStop();
            $this->LOGGER->info('Шкала успешно построена');
            PSCache::TIMELINES()->saveToCache($composition->getTimeLineJson(), $cacheKey);
            $this->LOGGER->info('Шкала сохранена в кеш под ключём: ' . $cacheKey);
            return $composition;
        } catch (Exception $ex) {
            $this->profilerStop(false);
            $this->LOGGER->info('Ошибка построения шкалы: ' . $ex->getMessage());
            throw $ex;
        }
    }

    /**
     * Метод возвращает представление для элемента хронологической шкалы
     * 
     * @param ArrayAdapter $params
     */
    public final function getTimeLineItemPresentation($ident, ArrayAdapter $params) {
        $this->profilerStart(__FUNCTION__);
        try {
            $presentation = $this->buildPresentation($ident, $params);
            $this->profilerStop();
            return $presentation;
        } catch (Exception $ex) {
            $this->profilerStop(false);
            throw $ex;
        }
    }

}

?>
