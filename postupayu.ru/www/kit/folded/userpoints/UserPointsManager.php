<?php

/**
 * Фолдинг для управления класами, выдающими очки пользователям.
 * 
 * Работает всё довольно просто, есть вусего три интерфейса:
 * 
 * 1. PointsGiver - наследуется теми классами фолдингов, которые действительно умеют выдавать очки пользователям.
 *    Например все классы данного фолдинга - наследники AbstractPointsGiver, умеют выдавать очки пользователям,
 *    поэтому AbstractPointsGiver наследует PointsGiver. При этом, например, только некоторые плагины умеют 
 *    выдавать очки пользователям.
 * 
 * 2. PointsGiverMaster - базовый интерфейс для базовых классов фолдинга, которые умеют выдавать очки пользователям.
 *    AbstractPointsGiver и BasePlugin наследуют этот интерфейс, так как все подклассы AbstractPointsGiver и некоторые
 *    подкласс BasePlugin умеют выдавать очки пользователям. В реализации методов PointsGiverMaster должен быть просто 
 *    вызван соответствующий метод класса UserPointsManager.
 * 
 * 3. PointsGiverFolding - базовый интерфейс для тех фолдингов, классы которых могут выдавать очки пользователям.
 *    Нужен для того, чтобы мы могли отличать эти фолдинги среди остальных.
 * 
 * 
 * @author azazello
 */
class UserPointsManager extends FoldedResources implements PointsGiverFolding {

    protected $RESOURCE_TYPES_ALLOWED = array(self::RTYPE_PHP, self::RTYPE_TPL);

    protected function isIncludeToList($ident, $list) {
        return false;
    }

    protected function onEntityChangedImpl($ident) {
        
    }

    public function getEntityName() {
        return 'Очки пользователя';
    }

    public function getFoldingGroup() {
        return 'userpoints';
    }

    public function getFoldingType() {
        return 'up';
    }

    public function getFoldingSubType() {
        
    }

    /**
     * Метод проверяет необходимость выдать очки текущему пользователю.
     * Для этого просто пробегаемся по полдингам, наследующим PointsGiverFolding,
     * а потом по всем классам этого фолдинга, наследующим PointsGiver.
     * 
     * ВАЖНО - нужно использовать именно getVisibleClassInsts (а не fcctssable), 
     * так как мы можем вызвать данный метод от администратора для другого пользователя, 
     * а администратору доступно больше фолдингов.
     * 
     * @return boolean - были ли даны очки
     */
    public function checkAllUserPoints(PsUser $user) {
        $given = false;
        /* @var $folding FoldedResources */
        foreach (Handlers::getInstance()->getFoldings() as $folding) {
            if ($folding instanceof PointsGiverFolding) {
                /* @var $inst FoldedClass */
                foreach ($folding->getVisibleClassInsts() as $inst) {
                    if ($inst instanceof PointsGiver) {
                        if ($this->checkPoints($inst, $user)) {
                            $given = true;
                        }
                    }
                }
            }
        }
        return $given;
    }

    /**
     * Метод пытается выдать очки пользователю на основе запроса к ajax {@link GivePointsCommon}
     */
    public function givePointsByRequest(PsUser $user, ArrayAdapter $request) {
        $fentity = Handlers::getInstance()->getFoldedEntityByUnique($request->str('fentity'));
        $class = $fentity->getClassInst();
        PsUtil::assertInstanceOf($class, 'PointsGiverRequest');
        $request->remove('fentity');
        $ctxt = new GivePointsContext($user->getId(), $fentity->getDbCode());
        $class->givePointsByRequest($ctxt, $request);
        return $this->checkGivePointsContext($user, $ctxt);
    }

    /**
     * Метод возвращает текстовое описание причины выдачи очков по коду выданных очков в базе
     * 
     * @return UserPointDO
     */
    public function getPointById($pointId, $userId = null) {
        return UserPointsBean::inst()->getPointById($pointId, $userId);
    }

    /**
     * Метод возвращает кол-во очков, о которых пользователь ещё не знает
     */
    public function getNewPointsCnt(PsUser $user) {
        return UserPointsBean::inst()->getNewPointsCnt($user->getId());
    }

    /**
     * Отмечает очки текущего пользователя, как просмотренные
     */
    public function markUserPointsShown(PsUser $user) {
        UserPointsBean::inst()->setPointsShown($user->getId());
    }

    /**
     * Метод загружает все очки, выданные пользователю
     */
    public function getAllUserPoints(PsUser $user) {
        return UserPointsBean::inst()->getAllUserPoints($user->getId());
    }

    /**
     * Метод, вызываемый для выдачи очков пользователю.
     * Должен вызываться из базовых классов фолдингов, наследующих {@link PointsGiverMaster}
     */
    public final function givePoints(FoldedClass $class, $func_get_args) {
        return $this->checkPointsImpl($class, $func_get_args, __FUNCTION__);
    }

    /**
     * Метод, вызываемый для проверки, нет ли у пользователя очков, положенных, но не выданных ему.
     * Должен вызываться из базовых классов фолдингов, наследующих {@link PointsGiverMaster}
     */
    public final function checkPoints(FoldedClass $class, PsUser $user) {
        return $this->checkPointsImpl($class, array($user), __FUNCTION__);
    }

    /**
     * Имплементация метода, выдающего очки пользователю
     */
    private function checkPointsImpl(FoldedClass $class, array $func_get_args, $method) {
        /**
         * Проверим, является ли класс наследником PointsGiver.
         * Менеджером он может и не быть, но гивером он быть обязан!:)
         */
        PsUtil::assertInstanceOf($class, 'PointsGiver');
        PsUtil::assertInstanceOf($class->getFoldedEntity()->getFolding(), 'PointsGiverFolding');
        //Проверим наличие метода-имплементации
        $method = $method . 'Impl';
        PsUtil::assertMethodExists($class, $method);

        /** @var PsUser */
        $user = $func_get_args[0];
        check_condition($user instanceof PsUser, 'Пользователь должен быть передан в первом аргументе');

        $ctxt = new GivePointsContext($user->getId(), $class->getFoldedEntity()->getDbCode());
        $func_get_args[0] = $ctxt;

        call_user_func_array(array($class, $method), $func_get_args);

        return $this->checkGivePointsContext($user, $ctxt);
    }

    /**
     * Метод проверяет контекст выдачи очков и выполняет все необходимые действия, если очки были даны
     */
    private function checkGivePointsContext(PsUser $user, GivePointsContext $ctxt) {
        $given = $ctxt->getGivenPoints();

        /* @var $pointDo UserPointDO */
        foreach ($given as $pointDo) {
            MSG_PointsGiven::inst()->sendSystemMsg($user, $pointDo->getPointId());
        }
        return count($given) > 0;
    }

    /** @return UserPointsManager */
    public static function inst() {
        return parent::inst();
    }

}

?>