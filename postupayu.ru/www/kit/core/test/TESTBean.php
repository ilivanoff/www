<?php

/**
 * Description of BPBean
 *
 * @author Admin
 */
class TESTBean extends BaseBean {

    public function testDates() {
//        $id = $this->insert('INSERT INTO test (dt) values (?)', array('2012-01-01'));
//        $id = $this->insert('INSERT INTO test (dt) values (?)', array('2013-01-01 12:30:45'));

        $arr = $this->getArray('select * from test');
        foreach ($arr as $data) {
            $dt = $data['dt'];
            print_r($data);
            echo strtotime($dt);
            echo ' ';
            print date('d.m.Y', strtotime($dt)) . "\n";
            br();
        }
    }

    public function createTestUser() {
        $id = $this->insert('
insert into users
  (user_name, b_sex, email, passwd, dt_reg, msg)
values
  (?, ?, ?, ?, UNIX_TIMESTAMP(), ?)', array(
            '',
            rand(SEX_BOY, SEX_GIRL),
            '@mail.ru',
            md5('1'), //
            getRandomString(100, true, 10)));

        $this->update('update users set user_name=?, email=? where id_user=?', array(
            "user$id",
            "$id@mail.ru",
            $id
        ));

        return $id;
    }

    public function getUserIds($userId = null) {
        return is_numeric($userId) ? array(1 * $userId) : $this->getIds('select id_user as id from users');
    }

    public function isTestUser($userId) {
        return $this->getCnt('select count(1) as cnt from users where id_user=? and user_name=? and b_admin=0', array($userId, "user$userId")) > 0;
    }

    public function removeTestUser($userId) {
        if ($this->isTestUser($userId)) {
            try {
                $this->update('delete from users where id_user=?', $userId);
            } catch (Exception $e) {
                
            }
        }
    }

    public function unsetAvatarUploads($userId) {
        $this->update('update users SET id_avatar = null WHERE id_user = ?', $userId);
        $this->update('delete from ps_upload where id_user=? and type=?', array($userId, UploadsBean::TYPE_AVATAR));
    }

    public function getRandomUserId() {
        $userId = $this->getRec("select id_user from users u where u.id_user!=2 order by RAND() limit 1");
        return (int) $userId['id_user'];
    }

    public function getRandomCommentId($commentsTable, $postId) {
        $commentId = $this->getRec("select id_comment from $commentsTable where id_post=? order by RAND() limit 1", $postId);
        return $commentId === null ? null : (int) $commentId['id_comment'];
    }

    public function deleteAllComments($commentsTable) {
        $this->update("update $commentsTable set id_parent=null");
        $this->update("delete from $commentsTable");
    }

    public function deleteTestPosts($postTable) {
        if ($postTable == 'train_post') {
            $this->update("delete from user_lessons where id_post in (select id_post from train_post where ident like 'test_%')");
        }
        $this->update("delete from $postTable where ident like 'test_%'");
    }

    public function deleteTestRubrics($rubricTable) {
        $this->update("delete from $rubricTable where ident like 'test_%'");
    }

    public function createRubric($rubricTable, $name, $ident, $content, $b_tpl = 0) {
        $max = $this->getRec("select max(id_rubric)+1 as id from $rubricTable");
        $nextId = $max['id'];
        $this->insert("insert into $rubricTable (id_rubric, name, ident, content, b_tpl) VALUES (?,?,?,?,?)", array($nextId, $name, $ident, $content, $b_tpl));
    }

    public function createPost($rubricTable, $postTable, $name, $ident, $content, $showcase, $b_tpl = 0) {
        $this->insert("
insert into $postTable (
   id_rubric
  ,name
  ,dt_publication
  ,b_show
  ,rev_count
  ,ident
  ,content
  ,content_showcase
  ,b_tpl
) VALUES (
   (select id_rubric from $rubricTable r order by RAND() limit 1)
  ,?  ,UNIX_TIMESTAMP()  ,1  ,0  ,?  ,?  ,?  ,?)", array($name, $ident, $content, $showcase, $b_tpl));
    }

    public function getAllMessages(DiscussionSettings $settings) {
        return $this->getArray('select * from ' . $settings->getTable() . ' where b_deleted=0');
    }

    public function cleanVotes() {
        $this->update('delete from ps_votes');
    }

    /** @return TESTBean */
    public static function inst() {
        return parent::inst();
    }

    protected function __construct() {
        PsDefines::assertProductionOff(__CLASS__);
        parent::__construct();
    }

}

?>
