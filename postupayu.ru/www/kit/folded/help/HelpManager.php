<?php

/**
 * Менеджер всплывающих подсказок
 *
 * @author azazello
 */
class HelpManager extends HelpResources implements BubbledFolding {

    public function getBubble($ident) {
        $PARAMS['body'] = $this->fetchTplImpl($ident);
        return PSSmarty::template('help/bubble.tpl', $PARAMS)->fetch();
    }

    /**
     * Ссылка на элемент библиотеки в виде всплывающей подсказки
     */
    public function getBubbleHref($ident, $text, ArrayAdapter $params) {
        if (!$ident) {
            return $text;
        }

        $entity = $this->getFoldedEntity($ident);
        if (!$entity) {
            return $text;
        }

        $unique = $entity->getUnique();

//        if (!$entity) {
        //return PsHtml::spanErr("Ссылка на несуществующий элемент подсказки [$unique]");
//        }

        return PsBubble::spanFoldedEntityBubble($text, $unique);
    }

    /** @return HelpManager */
    public static function inst() {
        return parent::inst();
    }

}

?>
