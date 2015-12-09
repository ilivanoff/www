<?php

/**
 * Фильтры Smarty.
 */
class SmartyFilters {

    private $CALLS;
    private $CALLTTL = 0;
    private $methodsMap = array(
        Smarty::FILTER_PRE => 'preCompile',
        //Smarty::FILTER_POST => 'postCompile',
        Smarty::FILTER_OUTPUT => 'output'
    );

    public function __construct(Smarty $smarty) {
        foreach ($this->methodsMap as $filterType => $method) {
            check_condition(method_exists($this, $method), "Method [$method] not exists in class " . __CLASS__);
            $this->CALLS[$method] = 0;
            $smarty->registerFilter($filterType, array($this, "_$method"));
        }
    }

    public function __call($method, $arguments) {
        $method = first_char_remove($method);

        $source = $arguments[0];

        /* @var $template Smarty_Internal_Template */
        $template = $arguments[1];
        $tplPath = $template->template_resource;

        //PsProfiler::inst(__CLASS__)->start($method);
        $result = $this->$method($source);
        //PsProfiler::inst(__CLASS__)->stop();

        $call = ++$this->CALLS[$method];
        $callttl = ++$this->CALLTTL;
        $callInfo = pad_right("[$callttl-$call].", 10, ' ');
        PsLogger::inst(__CLASS__)->info("{} {} filter called for template {}.", $callInfo, ucfirst($method), $tplPath);

        return $result;
    }

    /*
     * Вызывается до компиляции макета (построения php-файла).
     */

    public function preCompile($source) {
        $source = str_replace('\(', '{literal}\(', $source);
        $source = str_replace('\)', '\){/literal}', $source);

        $source = str_replace('\{', '{literal}\{', $source);
        $source = str_replace('\}', '\}{/literal}', $source);

        $source = str_replace('\[', '{literal}\[', $source);
        $source = str_replace('\]', '\]{/literal}', $source);

        $source = PsStrings::pregReplaceCyclic('/\$\$/', $source, array('{literal}$$', '$${/literal}'));

        //Обернём математический текст, например: &alpha; перейдёт в <span class="math_text">&alpha;</span>
        $source = TextFormulesProcessor::replaceMathText($source);

        //Заменим некоторые блоки на вызов методов данного класса
        $source = SmartyReplacesIf::preCompile($source);

        return $source;
    }

    /**
     * Вызывается после компиляции макета (построения php-файла).
     */
    public function postCompile($source) {
        return $source;
    }

    /**
     * Вызывается после компиляции и выполнения макета, но до показа пользователю.
     * TODO - функция вызывается довольно часто, и было бы не плохо вызывать её как можно реже
     * Метод сделан статическим, чтобы его можно было вызвать отдельно, но ьез необходимости создавать объект Smarty.
     */
    public static function output($source) {
        return TexImager::inst()->replaceInText($source);
    }

}

?>