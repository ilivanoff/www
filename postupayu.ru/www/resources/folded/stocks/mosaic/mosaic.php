<?php

/**
 * Акция - мозайка
 */
class ST_mosaic extends BaseStock {

    const DATA_ID = 'id';
    const MODE_TASK = 'task';
    const MODE_ANS = 'ans';

    private $imgId;

    protected function onInit(ArrayAdapter $stockParams) {
        $this->imgId = $stockParams->int(self::DATA_ID);
    }

    public function getShortView() {
        $data['info'] = MosaicImage::inst($this->imgId);
        return $this->fetchShort($data);
    }

    public function getFullView() {
        $mosaic = MosaicImage::inst($this->imgId);
        $data['info'] = $mosaic;
        $data['active'] = $this->isActive();
        $data['winner'] = $this->isActive() ? null : $mosaic->getWinnerAnswer();

        $html = $this->fetchFull($data);
        $jsParams['cellowners'] = $mosaic->getCellOwners();
        return new StockViewData($html, $jsParams);
    }

    public function getMosaicAnswer() {
        return $this->getInfo('stock' . $this->imgId, array('mode' => self::MODE_ANS));
    }

    public function getMosaicTask() {
        return $this->getInfo('stock' . $this->imgId, array('mode' => self::MODE_TASK));
    }

    /**
     * Действия
     */
    public function ajaxDeleteAnswer(ArrayAdapter $params) {
        MosaicImage::inst($this->imgId)->delUserAnswer($params->int('id'));
        return new AjaxSuccess();
    }

    public function ajaxOpenCells(ArrayAdapter $params) {
        MosaicImage::inst($this->imgId)->bindAllUserCells();
        return new AjaxSuccess();
    }

    public function formSaveAnswer($comment) {
        return new AjaxSuccess(MosaicImage::inst($this->imgId)->saveUserAnswer($comment));
    }

}

?>