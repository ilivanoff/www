<?php

class AP_APCommentsFormules extends BaseAdminPage {

    public function title() {
        return 'Формулы из комментариев';
    }

    public function buildContent() {
        $res = AdminPostsProcessor::inst()->saveCommentsFormules2Zip(DirManager::resources('sprites')->getDirItem());
        echo $this->getFoldedEntity()->fetchTpl(array('data' => $res));
    }

}

?>