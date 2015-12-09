<?php

class AP_APProfilers extends BaseAdminPage {

    const MODE_LIST = 'list';
    const MODE_PROFILER = 'profiler';

    public function title() {
        return 'Профайлеры ' . (PROFILING_ENABLED ? '(on)' : '(off)');
    }

    public static function url() {
        return self::pageUrl();
    }

    public static function urlProfiler($file) {
        return self::pageUrl(array('profiler' => $file));
    }

    public function buildContent() {
        $navigation = AdminPageNavigation::inst();

        $RQ = GetArrayAdapter::inst();
        $AL = PsProfiler::controller();

        $PARAMS['enabled'] = PROFILING_ENABLED;

        $mode = null;

        if ($RQ->has('profiler')) {
            $mode = self::MODE_PROFILER;
            $profiler = $RQ->str('profiler');
            $PARAMS['profilers'] = $AL->getStats($profiler);

            $navigation->addPath(self::url(), 'Профайлеры');
            $navigation->setCurrent($profiler);
        }

        if (!$mode) {
            $mode = self::MODE_LIST;
            $PARAMS['profilers'] = $AL->getStats();
            $navigation->setCurrent('Профайлеры');
        }

        $PARAMS['mode'] = $mode;
        echo $this->getFoldedEntity()->fetchTpl($PARAMS);
    }

    public function getSmartyParams4Resources() {
        return array('MATHJAX_DISABLE' => true);
    }

}

?>