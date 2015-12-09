<?php

/**
 * Класс отвечает за всё, что связано с преобразованием TeX формул в картинки:
 * 1. Получение графического представления картинки
 * 2. Замена формул в тексте на картинки/спрайты
 * 3. Получением по хешу формулы её текстового представления и т.д.
 * 
 * @author azazello
 */
final class TexImager extends AbstractSingleton {

    /** @var PsLoggerInterface */
    private $LOGGER;

    /** @var PsProfilerInterface */
    private $PROFILER;

    /** @var SimpleDataCache */
    private $CACHE;

    /** @var DirManager */
    private $DM;

    /** Кол-во произведённых замен */
    private $replaced = 0;

    /**
     * Метод заменяет TeX-формулы, заключённые в блоки \[...\] или \(...\),
     * на их представление в виде картинок или спрайтов.
     * 
     * @param bool $replcaAllToImaged - признак, нужно ли предварительно заменить все формулы
     *                                  на их представление, заменяемое на картинки
     */
    public function replaceInText($text, $replcaAllToImaged = false) {
        if (PsDefines::isReplaceFormulesWithImages()) {
            $text = TexTools::replaceTeX($text, array($this, '_replaceInTextImpl'), $replcaAllToImaged);
        }
        return $text;
    }

    /**
     * Метод выполняет фактическую замену TEX тега на его представление в виде картинки или спрайта.
     * 
     * @param str $original \[v_{\text{cp}}=\frac{\Delta S}{\Delta t}\]
     * @param str $formula    v_{\text{cp}}=\frac{\Delta S}{\Delta t}
     * @param str $type     block or inline
     */
    public function _replaceInTextImpl($original, $formula, $isBlock) {
        $type = $isBlock ? 'block' : 'inline';
        $replace = '';
        if ($formula) {
            /*
             * Проверяем, разрешена ли замена формул на спрайты и есть ли сейчас контекст.
             * Если всё выполнено, то это вовсе не означает, что необходимый спрайт подключен и есть 
             * спрайт для данной формулы.
             */
            if (PsDefines::isReplaceFormulesWithSprites() && FoldedContextWatcher::getInstance()->getSpritableContext()) {
                $replace = CssSpritesManager::getFormulaSprite(FoldedContextWatcher::getInstance()->getSpritableContext()->getSpritable(), $formula, array('TeX', $type));
            }

            /*
             * Если спрайта не нашлось, то заменяем на картинку.
             */
            if (!$replace) {
                $imgDi = $this->getImgDi($formula);
                $imgPath = $imgDi ? $imgDi->getRelPath() : null;

                if ($imgPath) {
                    $replace = PsHtml::img(array('src' => $imgPath, 'class' => "TeX $type", 'alt' => ''));
                } else {
                    $replace = $original;
                }
            }
        }

        /*
         * Логирование
         */
        if ($this->LOGGER->isEnabled()) {
            $this->LOGGER->info(++$this->replaced . ". Replacing $type TeX");
            $this->LOGGER->info("FULL:   $original");
            $this->LOGGER->info("CONTENT:  $formula");
            $this->LOGGER->info("SAVED:    $formula");
            $this->LOGGER->info("REPLACED: $replace");
            $this->LOGGER->info("\n");
        }

        return $replace;
    }

    /**
     * Метод возвращает DirItem элемента, содержащего картинку-представление формулы.
     * 
     * @param type $formula - текстовая формула
     * @param type $createIfNotExists - признак, стоит ли пытаться создавать картинку для формулы
     * @return DirItem
     */
    public function getImgDi($formula, $createIfNotExists = true) {
        $formula = TexTools::safeFormula($formula);

        if ($this->CACHE->has($formula)) {
            return $this->CACHE->get($formula);
        }

        $hash = TexTools::formulaHash($formula);

        $imgDI = $this->DM->getHashedDirItem(null, $hash, $hash, 'gif');

        if ($imgDI->isImg()) {
            $this->CACHE->set($formula, $imgDI);
            return $imgDI;
        }

        if (!$createIfNotExists) {
            return null;
        }

        //Создаём структуру директорий
        $imgDI->makePath();

        //Запрашиваем графическое представление
        $this->PROFILER->start($formula);
        try {
            //TODO - делать это локально, чтобы не зависить от стороннего сервиса
            $handle = fopen('http://latex.codecogs.com/gif.latex?' . rawurlencode($formula), 'r');
            $contents = '';
            while (!feof($handle)) {
                $contents .= fread($handle, 8192);
            }
            fclose($handle);
            $this->PROFILER->stop();

            //Сохраняем картинку в файл
            $imgDI->writeToFile($contents, true);
        } catch (Exception $ex) {
            //Останавливаем профайлер без сохранения
            $this->PROFILER->stop(false);
            //Делаем дамп ошибки
            ExceptionHandler::dumpError($ex, "Tex formula convertation requested for:\n$formula");
            //Попытаемся подчистить за собой, если мы что-то создали
            $imgDI->remove();
            //Пробрасываем эксепшн
            throw $ex;
        }

        //Сохраним текстовое представление
        $this->DM->getHashedDirItem(null, $hash, $hash, 'gif.tex')->writeToFile($formula, true);

        $this->CACHE->set($formula, $imgDI);

        return $imgDI;
    }

    /**
     * Метод возвращает оригинальный TEX по захешированному представлению
     */
    public function decodeTexFromHash($hash) {
        TexTools::assertValidFormulaHash($hash);
        return $this->DM->getHashedDirItem(null, $hash, $hash, 'gif.tex')->getFileContents(false, null);
    }

    /**
     * Извлекает формулы из переданного текста.
     * Возвращает коолекцию TEX->DirItem
     */
    public function extractTexImages($string, $createIfNotExists = true, $addDescr = false) {
        $formules = array();
        foreach (TexExtractor::inst($string, true)->getTexContents() as $formula) {
            $di = $this->getImgDi($formula, $createIfNotExists);
            if (!$di) {
                continue;
            }
            $formules[$formula] = $di;
            if (!$addDescr) {
                continue;
            }
            $formules["$formula.tex"] = DirItem::inst(null, $di->getAbsPath(), '.tex');
        }
        return $formules;
    }

    /**
     * Иетод возвращает все формулы, которые есть на данный момент
     */
    public function getAllFormules() {
        return $this->DM->getDirContentFull(null, DirItemFilter::IMAGES);
    }

    /** @return TexImager */
    public static function inst() {
        return parent::inst();
    }

    protected function __construct() {
        $this->LOGGER = PsLogger::inst(__CLASS__);
        $this->PROFILER = PsProfiler::inst(__CLASS__);
        $this->CACHE = SimpleDataCache::inst();
        $this->DM = DirManager::formules();
    }

}

?>