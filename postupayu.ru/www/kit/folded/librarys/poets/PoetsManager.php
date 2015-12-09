<?php

/**
 * Менеджер поэтов
 *
 * @author azazello
 */
class PoetsManager extends PoetsResources {

    private $VERSES = array();

    public function getVerses($poetIdent) {
        if (!array_key_exists($poetIdent, $this->VERSES)) {
            $this->VERSES[$poetIdent] = array();
            /** @var FoldedInfoTpl */
            foreach ($this->getInfoTpls($poetIdent, 'verses') as $tplDi) {
                $verse = Verse::inst($poetIdent, $tplDi);
                $this->VERSES[$poetIdent][$verse->getVerseIdent()] = $verse;
            }
        }
        return $this->VERSES[$poetIdent];
    }

    /** @return Verse */
    public function getVerse($poetIdent, $verseIdent) {
        $verses = $this->getVerses($poetIdent);
        if (!array_key_exists($verseIdent, $verses)) {
            return Verse::inst($poetIdent, $verseIdent);
        }
        return $verses[$verseIdent];
    }

    public function getTimeLineBuilderParams() {
        return new TimeLineBuilderParams();
    }

    protected function timeLineItemPresentation(LibItemDb $item, ArrayAdapter $params) {
        $data['item'] = $item;
        $data['verses'] = $this->getVerses($item->getIdent());
        return PSSmarty::template('timeline/poet.tpl', $data)->fetch();
    }

    /** @return PoetsManager */
    public static function inst() {
        return parent::inst();
    }

}

?>