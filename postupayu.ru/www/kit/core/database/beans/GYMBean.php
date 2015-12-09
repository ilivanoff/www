<?php

/**
 * Description of GYMBean
 *
 * @author Admin
 */
class GYMBean extends BaseBean {

    public function getExercisesList() {
        $datas = $this->getArray('
select e.id_gym_ex       as ex_id,
       e.name            as ex_name,
       g.id_muscle_group as gr_id,
       g.name            as gr_name
  from gym_exercises e, muscle_group g, gym_exercises2muscle_group m
 where e.id_gym_ex = m.id_gym_ex
   and g.id_muscle_group = m.id_muscle_group
 order by g.n_order asc, e.n_order asc, m.n_order asc');

        $result = array();

        foreach ($datas as $exData) {
            $exId = (int) $exData['ex_id'];
            if (!array_key_exists($exId, $result)) {
                $result[$exId] = new GymEx($exData);
            }

            $gr = new GymGr($exData);
            $result[$exId]->addGroup($gr);
        }

        return $result;
    }

    public function getGroupsList() {
        $datas = $this->getArray('
select e.id_gym_ex       as ex_id,
       e.name            as ex_name,
       g.id_muscle_group as gr_id,
       g.name            as gr_name
  from gym_exercises e, muscle_group g, gym_exercises2muscle_group m
 where e.id_gym_ex = m.id_gym_ex
   and g.id_muscle_group = m.id_muscle_group
 order by g.n_order asc, m.n_order asc, e.n_order asc');

        $result = array();

        foreach ($datas as $data) {
            $grId = (int) $data['gr_id'];
            if (!array_key_exists($grId, $result)) {
                $result[$grId] = new GymGr($data);
            }

            $ex = new GymEx($data);
            $result[$grId]->addEx($ex);
        }

        return $result;
    }

    private function isProgrammExists($programmId) {
        //todo также проверять и пользователя
        $count = $this->getCnt('select count(*) as cnt from gym_programm p where p.id_gym_programm=?', $programmId);
        return $count > 0;
    }

    private function deleteProgrammContent($programmId) {
        $this->update('delete from gym_sets where id_gym_programm=?', $programmId);
        $this->update('delete from gym_programm_exercises where id_gym_programm=?', $programmId);
    }

    public function deleteProgramm($programmId) {
        $this->deleteProgrammContent($programmId);
        $this->update('delete from gym_programm where id_gym_programm=?', $programmId);
    }

    public function saveProgramm(GymProgramm $programm) {
        $programmId = $programm->getId();

        if ($programmId && $this->isProgrammExists($programmId)) {
            $this->deleteProgrammContent($programmId);
            $this->update('update gym_programm set name=?, description=? where id_gym_programm=?', array(
                $programm->getName(),
                $programm->getComment(),
                $programmId));
        } else {
            $programmId = $this->insert('insert into gym_programm (name, description) values(?, ?)', array(
                $programm->getName(),
                $programm->getComment()));
        }

        $ex_num = 0;
        /* @var $ex GymProgrammEx */
        foreach ($programm->getExercises() as $ex) {
            $id_ex = $this->insert('insert into gym_programm_exercises (id_gym_programm, id_gym_ex, name, description, n_order) values (?, ?, ?, ?, ?)', array(
                $programmId,
                $ex->getId(),
                $ex->getName(),
                $ex->getComment(),
                ++$ex_num
                    ));

            $set_num = 0;
            /* @var $ex GymProgrammEx */
            foreach ($ex->getSets() as $set) {
                $this->insert('insert into gym_sets (id_gym_programm, id_gym_programm_exercise, value, n_order) values (?, ?, ?, ?)', array(
                    $programmId,
                    $id_ex,
                    $set,
                    ++$set_num
                ));
            }
        }

        return $programmId;
    }

    /*
      var programm = {
      id: null,
      name: nameValue,
      comment: null,
      datas: []
      }

      var data = {};
      data.id = null;
      data.name = null;
      data.sets = [];
      data.comment = null;
     */

    public function getProgramms() {
        //todo извлекать одним запросом.
        $programms = $this->getArray('
SELECT g.id_gym_programm AS id_prog,
       g.name AS prog_name,
       g.description AS prog_descr,
       e.id_gym_programm_exercise AS id_prog_ex,
       e.id_gym_ex,
       if(e.name is null, ex.name, e.name) AS ex_name,
       e.description AS ex_descr,
       s.value as set_val
  FROM gym_programm g,
             gym_programm_exercises e
          LEFT JOIN
             gym_exercises ex
          ON ex.id_gym_ex = e.id_gym_ex
       LEFT JOIN
          gym_sets s
       ON s.id_gym_programm_exercise = e.id_gym_programm_exercise
 WHERE g.id_gym_programm = e.id_gym_programm
ORDER BY g.id_gym_programm ASC, e.n_order ASC, s.n_order ASC');

        $progData = array();

        $curProg = null;
        foreach ($programms as $prog) {
            $progId = (int) $prog['id_prog'];

            if (!$curProg || $curProg->getId() != $progId) {
                $curProg = new GymProgramm(array_sift_out($prog, array(
                                    'id_prog' => 'id',
                                    'prog_name' => 'name',
                                    'prog_descr' => 'comment'
                                )));

                $progData[] = $curProg;
            }

            $progExId = (int) $prog['id_prog_ex'];
            $ex = $curProg->getExercise($progExId);
            if (!$ex) {
                $ex = new GymProgrammEx(array_sift_out($prog, array(
                                    'id_gym_ex' => 'id',
                                    'ex_name' => 'name',
                                    'ex_descr' => 'comment'
                                )));
                $curProg->addExercise($progExId, $ex);
            }

            if (!isEmptyInArray('set_val', $prog)) {
                $ex->addSet($prog['set_val']);
            }
        }

        return $progData;
    }

    /*
     * СИНГЛТОН
     */

    /** @return GYMBean */
    public static function inst() {
        return parent::inst();
    }

}

?>
