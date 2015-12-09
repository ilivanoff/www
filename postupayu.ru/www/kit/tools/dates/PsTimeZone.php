<?php

class PsTimeZone extends AbstractSingleton {

    private $cacheDTZ;
    private $selectHtml;
    private $curTZ;

    /** @return DateTimeZone */
    public function getCurrentDateTimeZone() {
        if (!isset($this->curTZ)) {
            $tzName = AuthManager::isAuthorized() ? PsUser::inst()->getTimezone() : null;
            $this->curTZ = $tzName ? $this->getDateTimeZone($tzName) : $this->getDateTimeZone();
        }
        return $this->curTZ;
    }

    public function isTimeZoneExists($tzName) {
        return $this->getDateTimeZone($tzName) != null;
    }

    /** @return DateTimeZone */
    public function getDateTimeZone($tzName = PS_DEFAULT_TZ) {
        if (!$this->cacheDTZ->has($tzName)) {
            $tz = null;
            try {
                $tz = new DateTimeZone($tzName);
            } catch (Exception $e) {
                $tz = null;
            }
            $this->cacheDTZ->set($tzName, $tz);
        }
        return $this->cacheDTZ->get($tzName);
    }

    public function getDateTimeZonesByOffset($seconds) {
        $zones = array();
        foreach (DateTimeZone::listIdentifiers() as $tzName) {
            $tz = $this->getDateTimeZone($tzName);
            if ($tz && $tz->getOffset(new DateTime()) == $seconds) {
                $zones[] = $tz;
            }
        }
        return $zones;
    }

    /*
      <select class="time_zones" name="{$smarty.const.FORM_PARAM_TIMEZONE}">
      {foreach $zones as $item}
      <option{if $def_zone==$item.name} selected="selected" class="current"{/if} value="{$item.name}" offset="{$item.offset}">{$item.name}&nbsp;&nbsp;({$item.gmt})</option>
      {/foreach}
      </select>
     */

    public function zonesSelectHtml() {
        if (!isset($this->selectHtml)) {
            $currentZone = $this->getCurrentDateTimeZone()->getName();

            $zones = array();
            foreach (DateTimeZone::listIdentifiers() as $tzName) {
                $tz = $this->getDateTimeZone($tzName);
                //#1
                if ($tz) {
                    $offsetS = $tz->getOffset(new DateTime()); // -- Сдвиг в секундах
                    $sign = $offsetS < 0 ? '-' : '+';

                    $secARR = DatesTools::inst()->parseSeconds($offsetS);
                    $offsetH = $secARR['hf'];
                    $offsetM = $secARR['m'];

                    $gmt = number_format("$offsetH.$offsetM", 2, ":", "");
                    $gmt = "GMT $sign$gmt";

                    $tzName = $tz->getName();

                    $option['value'] = $tzName;
                    $option['offset'] = $offsetS;
                    $option['content'] = "$tzName  ($gmt)";
                    if ($tzName == $currentZone) {
                        $option['class'] = 'current';
                        $option['selected'] = 1;
                    }

                    $zones[] = $option;
                }
                //#1
            }

            $selectAttrs['class'] = 'time_zones';
            $selectAttrs['name'] = FORM_PARAM_TIMEZONE;

            /*
             * Мы не передаём $currentZone внутрь, так как нам нужно ещё установить 
             * класс, который потом будет использован в js.
             */
            $this->selectHtml = PsHtml::select($selectAttrs, $zones);
        }
        return $this->selectHtml;
    }

    /** @return PsTimeZone */
    public static function inst() {
        return parent::inst();
    }

    protected function __construct() {
        $this->cacheDTZ = new SimpleDataCache();
    }

}

?>
