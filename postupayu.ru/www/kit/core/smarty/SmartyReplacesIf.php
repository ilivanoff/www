<?php

/**
 * Объявленные в данном классе public static final методы будут интерпретироваться, как блочные 
 * {block}{/block} функции и будут заменены на вызов метода данного класа.
 * 
 * Например блок:
 * 
 * {authed}
 * ...authed stuff...
 * {/authed}
 * 
 * Будет заменён в шаблоне на:
 * 
 * {if SmartyReplacesIf::authed()}
 * {/if}
 */
class SmartyReplacesIf {

    public static final function devmodeOrAdmin() {
        return self::devmode() || self::admin();
    }

    public static final function devmode() {
        return !PsDefines::isProduction();
    }

    public static final function production() {
        return PsDefines::isProduction();
    }

    public static final function admin() {
        return AuthManager::isAuthorizedAsAdmin();
    }

    public static final function notadmin() {
        return !self::admin();
    }

    public static final function authed() {
        return AuthManager::isAuthorized();
    }

    public static final function notauthed() {
        return !self::authed();
    }

    /**
     * Выполняет замену смарти-функций на вызов функций данного класса
     */
    public static function preCompile($source) {
        $class = __CLASS__;
        foreach (PsUtil::getClassMethods($class, true, true, true) as $method) {
            if ($method == __FUNCTION__) {
                continue;
            }
            $source = str_replace('{' . $method . '}', "{if $class::$method()}", $source);
            $source = str_replace('{/' . $method . '}', '{/if}', $source);
        }
        return $source;
    }

}

?>