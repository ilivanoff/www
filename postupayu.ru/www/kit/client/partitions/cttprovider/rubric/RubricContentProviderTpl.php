<?php

class RubricContentProviderTpl extends RubricContentProvider {

    public function getContent() {
        $type = $this->rubricContent->getPostType();
        $ident = $this->rubricContent->getIdent();

        $entity = $this->rp->getRubricsFolding()->getFoldedEntity($ident);
        return $entity->fetchTpl(array(), FoldedResources::FETCH_RETURN_CONTENT, true, 'content');
    }

}

?>