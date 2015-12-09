<?php

class AP_APLogs extends BaseAdminPage {

    const MODE_FOLDERS = 'folders';
    const MODE_FILES = 'files';
    const MODE_FILE = 'file';

    public function title() {
        return 'Логи ' . (LOGGING_ENABLED ? '(on)' : '(off)');
    }

    public static function urlFolders() {
        return self::pageUrl();
    }

    public static function urlFolder($folder) {
        return self::pageUrl(array('folder' => $folder));
    }

    public static function urlFile($folder, $file) {
        return self::pageUrl(array('folder' => $folder, 'file' => $file)) . '#' . $file;
    }

    public function buildContent() {
        $RQ = GetArrayAdapter::inst();
        $AL = PsLogger::controller();

        $PARAMS['num'] = $AL->getLastSessionId();
        $PARAMS['enabled'] = LOGGING_ENABLED;

        $mode = null;

        if ($RQ->has('file')) {
            $mode = self::MODE_FILE;
            $PARAMS['folder'] = $RQ->str('folder');
            $PARAMS['files'] = $AL->getLogFiles($RQ->str('folder'));
            $PARAMS['file'] = $AL->getLogFile($RQ->str('folder'), $RQ->str('file'));
        }

        if (!$mode && $RQ->has('folder')) {
            $mode = self::MODE_FILES;
            $PARAMS['folder'] = $RQ->str('folder');
            $PARAMS['files'] = $AL->getLogFiles($RQ->str('folder'));
        }

        if (!$mode) {
            $PARAMS['folders'] = $AL->getLogDirs();
            $mode = self::MODE_FOLDERS;
        }

        $PARAMS['mode'] = $mode;
        echo $this->getFoldedEntity()->fetchTpl($PARAMS);
    }

    public function getSmartyParams4Resources() {
        return array('MATHJAX_DISABLE' => true);
    }

}

?>