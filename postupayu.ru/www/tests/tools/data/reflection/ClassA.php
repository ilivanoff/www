<?php

/**
 * Description of ClassA
 *
 * @author azazello
 */
class ClassA implements InterfaceA {

    const CONST_A = 'A';
    const CONST_B = 'B';
    const CONST_C = 'C';
    const CONST_C2 = 'C';

    public $MY_PROP_DOUBLE = 0.0;
    public static $MY_PROP_ARR = array();
    public static $MY_PROP_INT = 1;
    protected static $MY_PROP_BOOL = true;

    public static function get__DIR__() {
        return __DIR__;
    }

    public static function get__FILE__() {
        return __FILE__;
    }

    public static function get__CLASS__() {
        return __CLASS__;
    }

    public static final function CONSTS() {
        return array('CONST_A' => 'A', 'CONST_B' => 'B', 'CONST_C' => 'C', 'CONST_C2' => 'C');
    }

    public function public_method() {
        
    }

    public final function public_final_method() {
        
    }

    protected function protected_method() {
        
    }

    protected final function protected_final_method() {
        
    }

    private function private_method() {
        
    }

    private final function private_final_method() {
        
    }

}

?>