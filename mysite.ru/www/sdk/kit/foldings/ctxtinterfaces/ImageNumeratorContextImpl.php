<?php

/**
 * Реализация интерфейса для тех контекстов, в рамках которых проходит нумерация формул
 */
class ImageNumeratorContextImpl extends FoldedContexAdapter implements ImageNumeratorContext {

    const IMG_NUM = 'IMG_NUM';
    const IMG_ID2NUM = 'IMG_ID2NUM';

    private function getNextImgNum($imageId, $doRegister) {
        $num = $imageId ? $this->ctxt->getMappedParam(self::IMG_ID2NUM, $imageId) : null;
        if ($doRegister && !is_numeric($num)) {
            $num = $this->ctxt->getNumAndIncrease(self::IMG_NUM);
            $this->ctxt->setMappedParam(self::IMG_ID2NUM, $imageId, $num);
        }
        return $num;
    }

    private function getImgElId($imageId) {
        return $this->ctxt->getFoldedEntity()->getUnique(IdHelper::blockImgId($imageId));
    }

    public function wrapBlockImgBox($imageId, array $attrs, $content) {
        $imageId = trim($imageId);
        $num = $this->getNextImgNum($imageId, true);
        if ($imageId) {
            $attrs['id'] = $this->getImgElId($imageId);
        }
        $content = str_replace(PsConst::NUM_REPLCASE_MACROS, PsConstJs::numeratorItemIndex(self::CSS_NUMERATOR_IMG, $num), trim($content));
        return PsHtml::div($attrs, $content);
    }

    public function getBlockImgHref($imageId) {
        $imageId = trim($imageId);
        $num = $this->getNextImgNum($imageId, false);

        if (!is_numeric($num)) {
            return PsHtml::spanErr("Ссылка на незарегистрированную картинку с идентификатором '$imageId'");
        }

        $boxId = $this->getImgElId($imageId);

        return PsBubble::spanById($boxId, "рис. " . PsConstJs::numeratorHrefIndex(self::CSS_NUMERATOR_IMG, $num));
    }

}

?>