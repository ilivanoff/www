<?php

abstract class AbstractAjaxAction {

    protected abstract function getAuthType();

    protected abstract function isCheckActivity();

    protected abstract function getRequiredParamKeys();

    protected abstract function executeImpl(ArrayAdapter $params);

    /**
     * =============
     * = ОБРАБОТКА =
     * =============
     */
    private $processed = false;

    /**
     * Основной метод, выполняющий выполнение Ajax действия.
     * 
     * @return AjaxSuccess
     */
    public final function execute() {
        $id = get_called_class();

        check_condition(!$this->processed, "Действие [$id] уже выполнено.");
        $this->processed = true;

        //Не будем портить глобальный массив $_REQUEST, создав копию адаптера
        $params = RequestArrayAdapter::inst()->copy();
        check_condition($params->str(AJAX_ACTION_PARAM) == $id, "Действие [$id] не может быть выполнено.");
        $params->remove(AJAX_ACTION_PARAM);

        //Проверка доступа
        AuthManager::checkAccess($this->getAuthType());

        //Если пользователь зарегистрирован, как администратор - подключим ресурсы админа
        ps_admin_on();

        //Проверка обязательных параметров
        foreach (to_array($this->getRequiredParamKeys()) as $key) {
            if (!$params->has($key)) {
                return "Не передан обязательный параметр [$key].";
            }
        }

        if ($this->isCheckActivity() && !ActivityWatcher::isCanMakeAction()) {
            return 'Таймаут не закончился.';
        }

        //Вызываем обработку данных
        PsProfiler::inst('AjaxProfiler')->start($id);
        $result = $this->executeImpl($params);
        PsProfiler::inst('AjaxProfiler')->stop();

        if (isEmpty($result)) {
            return "Действие [$id] выполнено некорректно - возвращён пустой результат.";
        }

        if (is_object($result) && ($result instanceof AjaxSuccess)) {
            //SUCCESS
            //Зарегистрируем активноcть пользователя (только в случае успеха, так как пользователь мог просто ошибиться в воде данных)
            if ($this->isCheckActivity()) {
                ActivityWatcher::registerActivity();
            }
        }

        return $result;
    }

}

?>