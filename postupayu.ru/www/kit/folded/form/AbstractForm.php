<?php

/**
 * Базовый класс всех форм. Классический пример работы с формой:
 * 

  if ($FORM->isValid4Process()) {
  $this->processForm($FORM->getData());
  } else if ($FORM->isErrorOccurred()) {
  $PARAMS['error'] = err_block($FORM->getError());
  }

 */
abstract class AbstractForm extends FoldedClass {

    protected $CAN_RESET = true;

    /**
     * В классе должны быть реализованы константы, начинающиеся на BUTTON_.
     * Это - кнопки, которые могут сабмитить форму.
     */
    private $buttons;
    private $buttonsAllowed;

    protected function _construct() {
        $this->buttons = PsUtil::getClassConsts(get_called_class(), 'BUTTON_');
        $this->buttonsAllowed = $this->buttons;
    }

    public function getId() {
        return $this->ident;
    }

    /**
     * Признак - нужно ли проверять активность (частоту сабмитов) формы.
     * Если нужно, то к форме будет добавлен специальный переметр для javascript.
     */
    private function isCheckActivity() {
        return $this instanceof CheckActivityForm;
    }

    /**
     * Метод возвращает признак - проверять ли капчу для формы.
     * Для таких форм капча будет добавлена перед панелью с кнопками.
     */
    private function isCheckCapture() {
        return $this instanceof CheckCaptureForm;
    }

    /**
     * Метод проверяет, должна ли форма быть защищена с помощью капчи.
     * Это выполняется в том случае, если пользователь не авторизован и форма работает с капчей.
     */
    private function isAddCapture() {
        return $this->isCheckCapture() && !AuthManager::isAuthorized();
    }

    /**
     * Выполнение обработки формы. В слечае успеха должен вернуться объект - наследник {@see FormSuccess}
     */
    protected abstract function processImpl(PostArrayAdapter $adapter, $button);

    /** @return AbstractForm */
    public static function getInstance() {
        return parent::inst();
    }

    private $hiddens = array();

    //Заполняет скрытые поля формы перед её показом
    public function setHidden($key, $value) {
        $this->hiddens[$key] = $value;
    }

    //Заполняет видимые поля формы перед её показом
    public function setParam($key, $value) {
        PostArrayAdapter::inst()->set($key, $value);
    }

    //Параметры для Smarty
    private $smartyParams = array();

    public function setSmartyParam($key, $value) {
        $this->smartyParams[$key] = $value;
    }

    /**
     * Возвращает признак - пытается ли пользователь засабмитить данную форму.
     * О валидности засабмиченных данных говорить ещё рано.
     */
    public final function isTryingSubmit() {
        return PostArrayAdapter::inst()->str(FORM_PARAM_ID) == $this->getId();
    }

    /**
     * Метод возвращает текст нажатой кнопки.
     */
    protected final function getSubmittedButton() {
        return PostArrayAdapter::inst()->str(FORM_PARAM_BUTTON);
    }

    /**
     * Проверяет, была ли засабмичена одна из переданных кнопок
     */
    public function isSubmittedByButton($buttons) {
        return in_array($this->getSubmittedButton(), to_array($buttons));
    }

    /**
     * =============
     * = ОБРАБОТКА =
     * =============
     */
    private $processed = false;
    private $result = null;
    private $error = null;

    /**
     * Основной метод, выполняющий обработку формы.
     * 
     * @return AbstractForm
     */
    private final function process() {
        if ($this->processed) {
            return $this; //---
        }
        $this->processed = true;

        if (!$this->isTryingSubmit()) {
            return $this; //---
        }

        //Проверка доступа
        $this->checkAccess();

        if ($this->isAddCapture() && !PsCapture::isValid()) {
            $this->error = 'Требуется валидный код с картинки';
            return $this; //---
        }

        //Если пользователь зарегистрирован, как администратор - подключим ресурсы админа
        ps_admin_on();

        $button = $this->getSubmittedButton();
        if (!$button) {
            $this->error = "Не передана нажатая кнопка для формы {$this->ident}.";
            return $this; //---
        }

        if (!$this->hasButton($button)) {
            $this->error = "Форма {$this->ident} засабмичена незарегистрированной кнопкой $button.";
            return $this; //---
        }

        if ($this->isCheckActivity() && !ActivityWatcher::isCanMakeAction()) {
            $this->error = 'Таймаут не закончился.';
            return $this; //---
        }

        //Вызываем обработку данных
        $result = null;

        try {
            $result = $this->processImpl(PostArrayAdapter::inst(), $button);
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            ExceptionHandler::dumpError($ex);
            return $this; //---
        }

        if (isEmpty($result)) {
            $this->error = "Форма {$this->ident} обработана некорректно - возвращён пустой объект.";
            return $this; //---
        }

        if (is_object($result) && ($result instanceof FormSuccess)) {
            //SUCCESS
            //Форсированно сбросим капчу (всё равно, работаем с ней или нет)
            PsCapture::reset();
            //Зарегистрируем активноcть пользователя (только в случае успеха, так как пользователь мог просто ошибиться в воде данных)
            if ($this->isCheckActivity()) {
                ActivityWatcher::registerActivity();
            }
            $this->result = $result;
        } else {
            //ERROR
            $this->error = $result;
        }

        return $this;
    }

    /**
     * Метод возвращает признак - можно ли работать с результатми сабмита данной формы.
     * Это возможно только если форма была забмичена и обработана без ошибок.
     */
    public final function isValid4Process() {
        return !!$this->process()->result;
    }

    /**
     * Метод возвращает признак - возникла ли ошибка в процессе сабмита форма.
     * Вернётся true только если форма была забмичена и обработана с ошибкой.
     * ВАЖНО! Если форма не была забмичена, то это не ошибка.
     */
    public final function isErrorOccurred() {
        return !!$this->process()->error;
    }

    /**
     * Возвращает данные обработки формы.
     * Вызов метода допускается, только если форма была засабмичена.
     */
    public function getData() {
        check_condition($this->isTryingSubmit(), 'Недопустимо вызывать метод ' . __FUNCTION__ . ' для незасабмиченной формы ' . $this->ident . '.');
        return $this->result;
    }

    /**
     * Возвращает ошибку обработки формы.
     * Вызов метода допускается, только если форма была засабмичена.
     */
    public final function getError() {
        check_condition($this->isTryingSubmit(), 'Недопустимо вызывать метод ' . __FUNCTION__ . ' для незасабмиченной формы ' . $this->ident . '.');
        return $this->error;
    }

    /**
     * ===================
     * = ПАРАМЕТРЫ ФОРМЫ =
     * ===================
     */
    public function getFormAction() {
        return PostArrayAdapter::inst()->str(FORM_PARAM_ACTION);
    }

    public function setFormAction($action) {
        $this->setHidden(FORM_PARAM_ACTION, $action);
    }

    private function hasButton($button) {
        return in_array($button, $this->buttons);
    }

    /**
     * Устанавливает кнопки, которые должны быть показаны на форме.
     */
    public function setButtons($button1 = null, $button2 = null, $button3 = null) {
        $this->buttonsAllowed = array();
        foreach (func_get_args() as $button) {
            $this->addButton($button);
        }
    }

    public function addButton($button) {
        check_condition($this->hasButton($button), "Форма {$this->ident} не может содержать кнопку $button.");
        $this->buttonsAllowed[] = $button;
    }

    public function removeButton($button) {
        check_condition($this->hasButton($button), "Форма {$this->ident} не может содержать кнопку $button.");
        $this->buttonsAllowed = array_diff($this->buttonsAllowed, array($button));
    }

    /**
     * ===========
     * = ФЕТЧИНГ =
     * ===========
     * 
     * Основной метод фетчинга формы. На вход можно передать hidden-поля.
     */
    private function fetchImpl(array $HIDDENS = null) {
        //HIDDEN PARAMS
        //1. Переданные извне
        $HIDDENS = to_array($HIDDENS);
        //2. Установленные
        $HIDDENS = array_merge($HIDDENS, $this->hiddens);
        //3. Код формы
        $HIDDENS[FORM_PARAM_ID] = $this->ident;


        //SMARTY PARAMS
        $SMARTY['form_id'] = $this->ident;
        $SMARTY['hiddens'] = $HIDDENS;
        $SMARTY['ajax_url'] = 'ajax/FormProcess.php';
        $SMARTY['html_hiddens'] = PsHtml::hiddens($HIDDENS); //HTML - hidden поля
        $SMARTY['html_buttons'] = ($this->isAddCapture() ? PsHtmlForm::capture() : '') . PsHtmlForm::submit($this->buttonsAllowed, $this->CAN_RESET); //HTML - submit buttons
        $SMARTY = array_merge($SMARTY, $this->smartyParams);

        //DO FETCH
        $content = $this->getFoldedEntity()->fetchTpl($SMARTY);

        //К шаблону формы добавим js-data
        $data = array();
        if ($this->isCheckActivity()) {
            $data['timer'] = 1;
        }
        if ($this instanceof BaseSearchForm) {
            $data['search'] = 1;
        }
        $data = PsHtml::data2string($data);
        if ($data) {
            $content = str_replace_first('<form', "<form $data", $content);
        }
        $content = $this->getFoldedEntity()->getResourcesLinks($content);

        return $content;
    }

    public final function fetch(array $hiddens = null) {
        return $this->fetchImpl($hiddens);
    }

    public final function display(array $hiddens = null) {
        echo $this->fetchImpl($hiddens);
    }

}

?>