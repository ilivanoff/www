<?php

/**
 * Класс, который позволяет расшифровать причину выдачи очков пользователю.
 *
 * @author azazello
 */
class UserPointDescriber {

    /** @var UserPointDO */
    private $point;

    /** @var PointsGiver */
    private $class;

    /** Короткое описание */
    private $title;

    /** Полное описание */
    private $content;

    public function title() {
        return isset($this->title) ? $this->title : $this->title = trim($this->class->shortReason($this->point));
    }

    public function content() {
        return isset($this->content) ? $this->content : $this->content = trim($this->class->fullReason($this->point));
    }

    private static $insts = array();

    /** @return UserPointDescriber */
    public static function inst(UserPointDO $point) {
        $pointId = $point->getPointId();
        return array_key_exists($pointId, self::$insts) ? self::$insts[$pointId] : self::$insts[$pointId] = new UserPointDescriber($point);
    }

    private function __construct(UserPointDO $point) {
        $this->point = $point;
        $this->class = FoldedResourcesManager::inst()->getFoldedEntityByDbCode($point->getReasonId())->getClassInst();
        PsUtil::assertInstanceOf($this->class, 'PointsGiver');
    }

}

?>
