<?php

final class MmediaManager extends AbstractSingleton {

    private $videoDM;
    private $audioDM;

    /*
     * Название файла может прийти как в виде "file.flv", так и в виде "file".
     * Вне зависимости от этого постер будем искать в виде "file.png".
     * 
     * Делаем так для того, чтобы вдальнейшем можно было подключать несколько 
     * файлов с одним расширением.
     * 
     * Пример тега:
     * {video dir='trainings' name='SympOfScience.flv'}
     */

    public function insertVideo($dirs, $name) {
        $data['file'] = $this->videoDM->httpFilePath($dirs, $name, 'flv');

        if ($this->videoDM->isFile($dirs, $name, 'png')) {
            $data['poster'] = $this->videoDM->httpFilePath($dirs, $name, 'png');
        }
        PSSmarty::template('mmedia/video_local.tpl', $data)->display();
    }

    /*
     * Пример тега:
     * {audio dir='trainings' name='SympOfScience.flv'}
     */

    public function insertAudio($dirs, $name) {
        $data['file'] = $this->audioDM->httpFilePath($dirs, $name, 'mp3');
        PSSmarty::template('mmedia/audio_local.tpl', $data)->display();
    }

    /** @return MmediaManager */
    public static function getInstance() {
        return parent::inst();
    }

    protected function __construct() {
        $this->videoDM = DirManager::mmedia('video');
        $this->audioDM = DirManager::mmedia('audio');
    }

}

?>
