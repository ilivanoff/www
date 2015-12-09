<?php

class IP_userlessonsstate extends BaseOfficePage {

    public function getTitle() {
        return 'Статусы уроков';
    }

    /*
      protected function getSmallShowInfo() {
      return TrainManager::inst()->hasPosts();
      }
     */

    protected function processRequest(ArrayAdapter $params) {
        return new IdentPageFilling();
    }

}

?>