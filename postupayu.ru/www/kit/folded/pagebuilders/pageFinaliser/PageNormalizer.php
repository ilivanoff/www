<?php

/**
 * Класс производит финальную нормальзацию страницы
 *
 * @author azazello
 */
class PageNormalizer extends AbstractPageFinalizer {

    /** Замена содержимого, например - текстовых полей */
    private $REPLACES = array();

    protected function doFinalize($PAGE_CONTENT) {
        //Сначала выделим то, что не должно попасть под нормализацию
        $this->addReplaces($PAGE_CONTENT);

        /*
         * Далее:
         * 1. Заменяем контент на макросы
         * 2. Проводим нормализацию
         * 3. Заменяем обратно макросы на контент
         */
        $hasReplaces = !empty($this->REPLACES);
        $replaceMacroses = array_to_string($this->REPLACES);
        if ($hasReplaces) {
            foreach ($this->REPLACES as $content => $replace) {
                $PAGE_CONTENT = str_replace($content, $replace, $PAGE_CONTENT);
            }
        }

        if ($hasReplaces && $this->LOGGER->isEnabled()) {
            $this->LOGGER->infoBox('PAGE WITH REPLACES ' . $replaceMacroses, $PAGE_CONTENT);
        }

        $PAGE_CONTENT = normalize_string($PAGE_CONTENT);

        if ($hasReplaces && $this->LOGGER->isEnabled()) {
            $this->LOGGER->infoBox('NORMALIZED PAGE WITH REPLACES ' . $replaceMacroses, $PAGE_CONTENT);
        }

        if ($hasReplaces) {
            foreach ($this->REPLACES as $content => $replace) {
                $PAGE_CONTENT = str_replace($replace, $content, $PAGE_CONTENT);
            }
        }

        if ($hasReplaces && $this->LOGGER->isEnabled()) {
            $this->LOGGER->infoBox('PAGE AFTER BACKREPLACE ' . $replaceMacroses, $PAGE_CONTENT);
        }

        return $PAGE_CONTENT;
    }

    private function addReplaces($PAGE_CONTENT) {
        //Прежде, чем вызвать нормализацию страницы, нужно вырезать все textareas
        $pattern = "/<textarea[^>]*>(.*?)<\/textarea>/si";
        $matches = array();
        preg_match_all($pattern, $PAGE_CONTENT, $matches);
        if (!empty($matches)) {
            $textareas = $matches[0]; //<textarea name="tpl">content</textarea>
            $contents = $matches[1];  //content
            for ($i = 0; $i < count($contents); $i++) {
                $content = $contents[$i];
                $textarea = $textareas[$i];
                if (isEmpty($content)) {
                    continue;
                }
                $this->addReplace($textarea);
            }
        }
    }

    /**
     * Метод регистрирует замену для контента
     */
    private function addReplace($content) {
        if (!array_key_exists($content, $this->REPLACES)) {
            $num = 1 + count($this->REPLACES);
            $macros = "~NRMRPL[[$num]]~";
            $this->REPLACES[$content] = $macros;

            $this->LOGGER->info("+ ADDED REPLACE '$macros':");
            $this->LOGGER->info('[' . $content . ']');
        }
    }

}

?>