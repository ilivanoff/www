<?php

/**
 * Менеджер, отвечающий за склонение слов по падежам.
 * Основан на запросе сервиса яндекса.
 * 
 * Вопросы по падежам: http://ic.pics.livejournal.com/u3poccuu/31290154/385691/385691_original.gif
 * 
 * Именительный:  кто? что?
 * Родительный:   кого? чего?
 * Дательный:     кому? чему?
 * Винительный:   кого? что?
 * Творительный:  кем? чем?
 * Предложный:    ком? чём?
 *
 * @author azazello
 */
final class InflectsManager extends AbstractSingleton {
    /**
     * Падежи
     */

    const TYPE_ORIG = 0; //Оригинальное слово
    const TYPE_IM = 1; //Именительный
    const TYPE_ROD = 2; //Родительный
    const TYPE_DAT = 3; //Дательный
    const TYPE_VIN = 4; //Винительный
    const TYPE_TVAR = 5; //Творительный
    const TYPE_PREDL = 6; //Предложный

    /** @var PsLoggerInterface */

    private $LOGGER;

    /** @var PsProfilerInterface */
    private $PROFILER;

    /** @var SimpleDataCache */
    private $CACHE;

    /**
     * Основной метод, получающий склонение слова по падежам.
     * Подсмотрено здесь: http://forum.dklab.ru/viewtopic.php?p=169151
     * 
     * @param type $word - склоняемое слово
     * @return type - массив, в котором 0 - исходная форма слова, 1...6 - номера падежей.
     */
    private function loadInflectionImpl($word) {
        $this->PROFILER->start('Loading word inflection');
        $cont = file_get_contents('http://export.yandex.ru/inflect.xml?name=' . rawurlencode($word));
        $this->PROFILER->stop();
        if ($cont) {
            $pattern = "/<inflection case=\"(\d)\">(.+?)<\/inflection>/si";
            preg_match_all($pattern, $cont, $matches);
            //Проверим, что вернулось 6 падежей
            if (array_key_exists(1, $matches) && array_key_exists(2, $matches) && count($matches[1]) == 6 && count($matches[2]) == 6) {
                return array(0 => $word) + array_combine($matches[1], $matches[2]);
            }
        }
        return null;
    }

    /**
     * Метод возвращает склонение слова в заданном падеже
     * 
     * @param string $word - слово
     * @param string $type - требуемый падеж
     * @return type
     */
    public function getInflection($word, $type = self::TYPE_ORIG) {
        if ($type == self::TYPE_ORIG) {
            return $word; //Сразу вернём оригинальное значение
        }
        PsUtil::assertClassHasConstVithValue(__CLASS__, 'TYPE_', $type);
        return array_get_value($type, $this->getInflections($word));
    }

    /**
     * Метод возвращает формы слова во всех падежах
     * 
     * @param type $word
     * @return array - все склонения слова в виде массива, где под индексом 0 - оригинальное значение
     */
    public function getInflections($word) {
        $word = PsCheck::notEmptyString(trim($word));

        if ($this->CACHE->has($word)) {
            return $this->CACHE->get($word);
        }

        $this->LOGGER->info();
        $this->LOGGER->info('> Запрошено склонение для слова [{}]', $word);
        //$fileName = iconv('UTF-8', 'cp1251', $word);

        /*
         * Ищем в БД
         */
        $inflections = InflectsBean::inst()->getInflections($word);
        if (is_array($inflections)) {
            $this->LOGGER->info('< Cклонение для [{}] найдено в базе: {}', $word, array_to_string($inflections));
            return $this->CACHE->set($word, $inflections);
        }

        /*
         * Загружаем с сервиса
         */
        $inflections = $this->loadInflectionImpl($word);
        if (is_array($inflections) && count($inflections) == 7) {
            $this->LOGGER->info('< Склонение для [{}] успешно загружено: {}', $word, array_to_string($inflections));
            //Не забудем сохранить полеченное склонение для слова
            InflectsBean::inst()->saveInflections($inflections);
            return $this->CACHE->set($word, $inflections);
        }

        /*
         * Загрузить не удалось, возвращаем балванку
         */
        $inflections = array_fill(0, 7, $word);
        $this->LOGGER->info('< Склонение для [{}] не определено, возвращаем "болванку": {}', $word, array_to_string($inflections));
        return $this->CACHE->set($word, $inflections);
    }

    /** @return InflectsManager */
    public static function inst() {
        return parent::inst();
    }

    protected function __construct() {
        PsUtil::assertClassHasDifferentConstValues(__CLASS__, 'TYPE_');

        $this->LOGGER = PsLogger::inst(__CLASS__);
        $this->PROFILER = PsProfiler::inst(__CLASS__);
        $this->CACHE = new SimpleDataCache();
    }

}

?>