<?php

class BP_index extends BasicPage {

    public static function registerWebPages() {
        WebPages::register('index.php', 'Главная', BASE_PAGE_INDEX, PB_basic::getIdent(), AuthManager::AUTH_TYPE_NO_MATTER);
    }

    public function doProcess(RequestArrayAdapter $params) {
        
    }

    public function buildContent() {
        //TODO - for test only
        //echo Handlers::getInstance()->getDiscussionController(POST_TYPE_ISSUE)->buildDiscussion(true, 1);
//        echo MagManager::inst()->getDiscussionController()->buildDiscussion(true, 5);
//        echo MagManager::inst()->getDiscussionController()->buildDiscussion(true, 5);
        //echo IP_userpoints::getInstance()->getContent(RequestArrayAdapter::inst())->getContent();
//        echo UserPointsManager::inst()->getUserPointsTable(PsUser::inst());
        echo $this->getFoldedEntity()->fetchTpl();
// your registration data
        $mrh_login = "test";      // your login here
        $mrh_pass1 = "securepass1";   // merchant pass1 here
// order properties
        $inv_id = 5;        // shop's invoice number 
        // (unique for shop's lifetime)
        $inv_desc = "desc";   // invoice desc
        $out_summ = "5.12";   // invoice summ
// build CRC value
        $crc = md5("$mrh_login:$out_summ:$inv_id:$mrh_pass1");

// build URL
        $url = "https://auth.robokassa.ru/Merchant/Index.aspx?MrchLogin=$mrh_login&amp;" .
                "OutSum=$out_summ&amp;InvId=$inv_id&amp;Desc=$inv_desc&amp;SignatureValue=$crc";

// print URL if you need
        echo "<a href='$url'>Payment link</a>";
        /*
          $mrh_login = "demo";
          $mrh_pass1 = "password_1";
          $inv_id = 0;
          $inv_desc = "Техническая документация по ROBOKASSA";
          $out_summ = "8.96";
          $crc = md5("$mrh_login:$out_summ:$inv_id:$mrh_pass1");
          print "<form action='http://test.robokassa.ru/Index.aspx' method=POST>" . "<input type=hidden name=MrchLogin value=$mrh_login>" . "<input type=hidden name=OutSum value=$out_summ>" . "<input type=hidden name=InvId value=$inv_id>" . "<input type=hidden name=Desc value='$inv_desc'>" . "<input type=hidden name=SignatureValue value=$crc>" . "<input type=submit value='Оплатить'>" . "</form>";
         */
    }

    public function getJsParams() {
        
    }

    public function getSmartyParams4Resources() {
        
    }

}

?>