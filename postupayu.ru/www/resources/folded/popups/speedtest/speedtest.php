<?php

class PP_speedtest extends BasePopupPage {

    const REQUESTS_COUNT = 5;

    public function getAuthType() {
        return AuthManager::AUTH_TYPE_AUTHORIZED_AS_ADMIN;
    }

    private $url;

    public function doProcess(ArrayAdapter $params) {
        $this->url = $params->str('url');
    }

    public function getTitle() {
        return 'Измерение скорости загрузки страницы';
    }

    public function getDescr() {
        return $this->getTitle();
    }

    public function getJsParams() {
        
    }

    public function buildContent() {
        PsUtil::startUnlimitedMode();

        $s = Secundomer::inst();

        $data = array();

        for ($num = 0; $num <= self::REQUESTS_COUNT; $num++) {
            $s->start();
            file_get_contents($this->url);
            $s->stop();

            $data[$num]['time'] = $s->getTime();
            $data[$num]['total'] = $s->getTotalTime();

            if ($num == 0) {
                //Запросим один раз, чтобы сработало кеширование, если оно включено
                $s->clear();
            }
        }

        $params = array(
            'url' => $this->url,
            'data' => $data,
            'average' => $s->getAverage());

        echo $this->getFoldedEntity()->fetchTpl($params);
    }

    public function getSmartyParams4Resources() {
        
    }

}

?>
