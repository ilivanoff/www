<?php

final class MosaicImage {

    const BG_COLOR = 0xF3F3F3;

    private $id;         //Код картинки
    private $width;      //Ширина картинки
    private $height;     //Высота картинки
    private $cellsXcnt;  //Кол-во ячеек по горизонтали
    private $cellsYcnt;  //Кол-во ячеек по вертикали
    private $cellsTotal; //Общее кол-во ячеек
    private $cellWidth;  //Ширина ячейки
    private $cellHeight; //Высота ячеки

    /** @var MosaicImgBean */
    private $BEAN;

    /** @var PsLoggerInterface */
    private $LOGGER;

    /** @var PsProfilerInterface */
    private $PROFILER;

    private function __construct($id) {
        $this->id = 1 * $id;
        $this->BEAN = MosaicImgBean::inst();

        $params = ArrayAdapter::inst($this->BEAN->getImgInfo($this->id));
        $this->width = $params->int('w');
        $this->height = $params->int('h');
        $this->cellsXcnt = $params->int('cx');
        $this->cellsYcnt = $params->int('cy');
        $this->cellWidth = $params->int('cw');
        $this->cellHeight = $params->int('ch');
        $this->cellsTotal = $this->cellsXcnt * $this->cellsYcnt;

        $this->LOGGER = PsLogger::inst(__CLASS__);
        $this->PROFILER = PsProfiler::inst(__CLASS__);

        $this->tryResyncImg();
    }

    public function getId() {
        return $this->id;
    }

    public function getWidth() {
        return $this->width;
    }

    public function getHeight() {
        return $this->height;
    }

    public function getTotalCellsCnt() {
        return $this->cellsTotal;
    }

    public function getOwnedCellsCnt() {
        return $this->BEAN->getOwnedCellsCnt($this->id);
    }

    public function getFreeCellsCnt() {
        return $this->cellsTotal - $this->getOwnedCellsCnt();
    }

    public function hasNotOwnedCells() {
        return $this->cellsTotal > $this->getOwnedCellsCnt();
    }

    /** @return DirItem */
    private function diSrc() {
        return DirManager::stuff('mosaic')->getDirItem(null, $this->id, 'jpg');
    }

    /** @return DirItem */
    private function diDst() {
        return DirManager::autogen('mosaic')->getDirItem(null, $this->id, 'jpg');
    }

    public function getRelPath() {
        return $this->diDst()->getRelPath();
    }

    /**
     * Загружает информацию из кеша.
     * Кешируем только то, что для нас не критично и не может привести к некорректной работе, если мы, например, забудем
     * обновить кеш. Одно дело, если пользователь увидит картинку, на которой отображаются не все ячейки, но другое - если он сможет привязать большее ячеек,
     * чем ему позволено.
     */

    const CACHABLE_AREAS = 'areas';
    const CACHABLE_STATISTIC = 'staticstic';

    /**
     * Подписью кеша служит кол-во ячеек, принадлежащих пользователям.
     */
    private function getCachable($key) {
        $sign = $this->getOwnedCellsCnt();
        $DATA = PSCache::MOSAIC()->getFromCache($this->id, PsUtil::getClassConsts(__CLASS__, 'CACHABLE_'), $sign);
        if (!is_array($DATA)) {
            $DATA = array();
            $ownedCells = $this->BEAN->getOwnedCells($this->id);
            $sign = count($ownedCells); //Честно посчитаем подпись по кол-ву ячеек, с которым была построена карта
            $DATA[self::CACHABLE_AREAS] = MosaicImageCellsCompositor::area($ownedCells, $this->cellWidth, $this->cellHeight);
            $DATA[self::CACHABLE_STATISTIC] = $this->BEAN->getStatictic($this->id);
            PSCache::MOSAIC()->saveToCache($DATA, $this->id, $sign);
        }
        return array_get_value($key, $DATA);
    }

    /**
     * Html - области на картинке
     */
    public function getHtmlAreas() {
        return $this->getCachable(self::CACHABLE_AREAS);
    }

    /**
     * Статистика - сколько пользователей заняли сколько ячеек
     */
    public function getStatistic() {
        return $this->getCachable(self::CACHABLE_STATISTIC);
    }

    public function hasStatistic() {
        return !!count($this->getStatistic());
    }

    /*
     * =====================
     * = КОПИРОВАНИЕ ЯЧЕЕК =
     * =====================
     */

    /**
     * Проверит, нужно ли провести синхронизацию состояния с состоянием в базе, и, если нужно, проведёт её.
     */
    private function tryResyncImg() {
        if (!$this->diDst()->isImg()) {
            $this->resyncImg();
        }
    }

    /**
     * Синхронизация состояния в базе с картинкой
     */
    public function resyncImg() {
        PsLock::lock(__CLASS__ . $this->id);
        try {
            $this->LOGGER->info("Resync img {$this->id} with db.");
            $this->diDst()->remove();
            $this->copyCells($this->BEAN->getOwnedCells($this->id));
        } catch (Exception $ex) {
            PsLock::unlock();
            throw $ex;
        }
        PsLock::unlock();
    }

    /**
     * Метод открывает на картинке все ячейки, которые может открыть пользователь
     */
    public function bindAllUserCells($userId = null) {
        $userId = AuthManager::extractUserId($userId);
        $canOpenCnt = $this->getUserCanOpenCellsCnt($userId);
        if ($canOpenCnt <= 0) {
            return; //---
        }
        $this->LOGGER->info("Binding all cells of user $userId to img {$this->id}, can open $canOpenCnt cells.");

        PsLock::lock(__CLASS__ . $this->id);
        try {
            //Запросим ячейки снова, так как пока мы ждали блокировку, могло что-то случиться...
            $canOpenCnt = $this->getUserCanOpenCellsCnt($userId);
            if ($canOpenCnt > 0) {
                $this->copyCells($this->BEAN->getCells4UserBind($this->id, $canOpenCnt), $userId);
            }
        } catch (Exception $ex) {
            PsLock::unlock();
            throw $ex;
        }
        PsLock::unlock();
    }

    /**
     * Копирует ячейки из картинки-источника в картинку-мозайку.
     * 
     * @param array $INFO - информация о картинке
     * @param array $cells - ячейки из БД, которые будут скопированы
     * @param type $userId - код пользователя, к которому ячейки будут привязаны
     */
    private function copyCells(array $cells, $userId = null) {
        //Проверим, есть ли сейчас "чистая" картинка, на которую мы будем копировать ячейки
        if (!$this->diDst()->isImg()) {
            SimpleImage::inst()->create($this->width, $this->height, self::BG_COLOR)->save($this->diDst())->close();
        }

        if (empty($cells)) {
            return; //---
        }

        if ($this->LOGGER->isEnabled()) {
            $this->LOGGER->info('Copy cells of image ' . $this->id . ', user for bind: ' . var_export($userId, true));
            $this->LOGGER->info('Not processed cells: ' . print_r($cells, true));
        }

        PsUtil::startUnlimitedMode();

        $this->PROFILER->start('Copy cells of img ' . $this->id);

        //1. Разберёмся с ячейками - привяжем к пользователю те их них, которые никому не принадлежат
        foreach ($cells as $cell) {
            $n_part = 1 * $cell['n_part'];
            $owned = !!$cell['owned'];

            //Ячейка должна кому-то принадлежать, либо быть привязана к переданному пользователю
            check_condition($owned || is_numeric($userId), "Ячейка {$this->id}.$n_part никому не принадлежит.");

            if (!$owned) {
                //Если ячейка уже привязана к пользователю, то не будем лишний раз дёргать базу
                $this->LOGGER->info('{}. Cell binded to user {}', $n_part, $userId);
                $this->BEAN->markAsOwned($userId, $this->id, $n_part);
            }
        }

        //2. Копируем ячейки, предварительно объединив их
        $srcImg = SimpleImage::inst()->load($this->diSrc());
        $dstImg = SimpleImage::inst()->load($this->diDst());

        $unioned = MosaicImageCellsCompositor::union($cells, $this->cellWidth, $this->cellHeight, false);

        foreach ($unioned as $cell) {
            $x1 = $cell[0];
            $y1 = $cell[1];
            $x2 = $cell[2];
            $y2 = $cell[3];
            $w = $x2 - $x1;
            $h = $y2 - $y1;

            $dstImg->copyFromAnother($srcImg, $x1, $y1, $x1, $y1, $w, $h)->save();

            $this->LOGGER->info('Copied rectangle: {}x{}-{}x{}', $x1, $y1, $x2, $y2);
        }

        $srcImg->close();
        $dstImg->close();

        $this->PROFILER->stop();
    }

    /*
     * СЛЕДУЮЩИЕ МЕТОДЫ НЕ ТРЕБУЮТ ПЕРЕДАЧИ ИЗВНЕ И ЗАГРУЖАЮТСЯ ДИНАМИЧЕСКИ
     */

    public function getUserOwnedCellsCnt($userId = null) {
        return $this->BEAN->getOwnedCellsCnt($this->id, AuthManager::extractUserId($userId));
    }

    public function getUserCanOpenCellsCnt($userId = null) {
        $userId = AuthManager::extractUserId($userId);

        //1. Сколько у пользователя очков?
        $pointsCnt = UserPointsBean::inst()->getPointsCnt($userId);
        if ($pointsCnt <= 0) {
            return 0;
        }

        //2. Сколько ячеек данный пользователь уже открыл?
        $canOpen = $pointsCnt - $this->getUserOwnedCellsCnt($userId);
        if ($canOpen <= 0) {
            return 0;
        }

        //3. Вернём минимум из того, что пользователь может открыть и того, сколько всего ячеек осталось
        return min($canOpen, $this->getFreeCellsCnt());
    }

    /**
     * Возвращает информацию о владельцах ячеек для показа всплывающей подсказки
     */
    public function getCellOwners() {
        $result = array();
        /** @var PsUser */
        foreach ($this->BEAN->getImgUsers($this->id) as $user) {
            $result[$user->getId()] = array(
                //Мы должны сразу применить фильтры смарти, так как владельцы картинок будут возвращены,
                //как js-params, и к ним фильтры применены не будут
                'msg' => SmartyFilters::output($user->getMsg()),
                'name' => $user->getName(),
                'avatar' => $user->getAvatarRelPath('42x')
            );
        }

        return $result;
    }

    public function saveUserAnswer($answer, $userId = null) {
        $userId = AuthManager::extractUserId($userId);
        $this->BEAN->saveImgAnswer($this->id, $userId, $answer);
        return $this->getUserAnswerHtml($userId);
    }

    public function delUserAnswer($ansId) {
        $this->BEAN->delImgAnswer($ansId, $this->id, AuthManager::getUserId());
    }

    public function getUserAnswerHtml($userId = null) {
        /* @var $ans UserAnsDO */
        $ans = $this->BEAN->getUserAnswer($this->id, AuthManager::extractUserId($userId));
        return $ans ? PSSmarty::template('mosaic/user_answer.tpl', array('ans' => $ans))->fetch() : null;
    }

    /** @var $ans UserAnsDO */
    public function getWinnerAnswer() {
        return $this->BEAN->getWinnerAnswer($this->id);
    }

    /*
     * ======================
     * = ФАБРИКА СИНГЛТОНОВ =
     * ======================
     */

    private static $insts = array();

    /**
     * Единственный вариант создать экземпляр экземпляр класса, работающего с картинкой-мозайкой
     * 
     * @return MosaicImage
     */
    public static function inst($id) {
        if (!array_key_exists($id, self::$insts)) {
            check_condition(is_inumeric($id), "Некорректный код картинки-мозайки: [$id].");
            self::$insts[$id] = new self($id);
        }
        return self::$insts[$id];
    }

}

?>
