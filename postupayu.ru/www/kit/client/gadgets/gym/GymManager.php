<?php

class GymManager extends AbstractSingleton {

    private $dirsMagager;
    private $dim = 156;

    public function relCoverPath($exId, $name = 'cover') {
        return $this->dirsMagager->relFilePath($exId, $name, 'jpg');
    }

    public function relCoverPathSmall($exId) {
        return $this->relCoverPath($exId, 'cover' . $this->dim . 'x' . $this->dim);
    }

    private function absCoverPath($exId, $name = 'cover') {
        return $this->dirsMagager->absFilePath($exId, $name, 'jpg');
    }

    private function absCoverPathSmall($exId) {
        return $this->absCoverPath($exId, 'cover' . $this->dim . 'x' . $this->dim);
    }

    protected function __construct() {
        $this->dirsMagager = DirManager::images('GymExercises');
    }

    /*
     * Упражнения
     */

    private $exercises;

    public function getExercises() {
        if (!isset($this->exercises)) {
            $this->exercises = GYMBean::inst()->getExercisesList();
        }
        return $this->exercises;
    }

    public function getExercise($exId) {
        $data = $this->getExercises();
        return value_Array((int) $exId, $data);
    }

    /*
     * Группа мышц
     */

    private $groups;

    public function getGroups() {
        if (!isset($this->groups)) {
            $this->groups = GYMBean::inst()->getGroupsList();
        }
        return $this->groups;
    }

    /*
     * Создаёт картинки
     */

    public function createSmallCovers($forceRecr = false) {

        /* @var $ex GymEx */
        foreach ($this->getExercises() as $ex) {

            $curImg = $this->absCoverPath($ex->getId());

            if (!PsImg::isImg($curImg)) {
                continue;
            }

            $imgNew = $this->absCoverPathSmall($ex->getId());

            if (!PsImg::isImg($imgNew) || $forceRecr) {
                SimpleImage::inst()->load($curImg)->resizeSmart($this->dim, $this->dim)->save($imgNew)->close();
            }
        }
    }

    /*
     * Получение класса и групп (для групп мышц, на которые действует упражнение)
     */

    public function getClass(GymEx $ex) {
        $data = array();
        /* @var $gr GymGr */
        foreach ($ex->getGroups() as $gr) {
            $data[] = 'g' . $gr->getId();
        }
        return concat($data);
    }

    /*
     * Работа с программами тренировок
     */

    public function saveProgramm(GymProgramm $programm) {
        return GYMBean::inst()->saveProgramm($programm);
    }

    public function deleteProgramm($programmId) {
        return GYMBean::inst()->deleteProgramm($programmId);
    }

    /*
     * Работа с программами тренировок
     */

    public function getProgramms() {
        return GYMBean::inst()->getProgramms();
    }

    public function getProgrammsAsArrays() {
        $result = array();
        /* @var $programm GymProgramm */
        foreach (GYMBean::inst()->getProgramms() as $programm) {
            $result[] = $programm->toArray();
        }
        return $result;
    }

    /** @return GymManager */
    public static function getInstance() {
        return parent::inst();
    }

}

?>