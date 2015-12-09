<?php

class PsMathEvaluator extends AbstractSingleton {

    private $EVALUATOR;

    public function e($expr) {
        return $this->EVALUATOR->e($expr);
    }

    public function lastError() {
        return $this->EVALUATOR->last_error;
    }

    /** @return PsMathEvaluator */
    public static function inst() {
        return parent::inst();
    }

    protected function __construct() {
        ExternalPluginsManager::MathEvaluator();
        $this->EVALUATOR = new EvalMath();
        $this->EVALUATOR->suppress_errors = false;
    }

}

?>