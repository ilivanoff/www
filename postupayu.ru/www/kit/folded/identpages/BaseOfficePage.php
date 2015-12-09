<?php

abstract class BaseOfficePage extends BaseIdentPage {

    public function getAuthType() {
        return AuthManager::AUTH_TYPE_AUTHORIZED;
    }

    public final function smallOfficeLiContent() {
        $title = $this->getTitle();
        $state = '';

        if ($this instanceof NumerableOfficePage) {
            $state = $this->getNumericState();
            $state = $state ? PsHtml::span(array('class' => 'cnt'), "($state)") : null;
            $HREF['class'][] = 'stetable';
        }

        $coverRel = $this->foldedEntity->getCover()->getRelPath();
        $HREF['style'] = array('background-image' => "url($coverRel)");

        //return "<a class=\"$ident ip-opener\" title=\"$title\" href=\"#$ident\">$title</a> $count";
        return $this->getIdentPageHref($HREF, "$title$state");
    }

}

?>