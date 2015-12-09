<?php

require_once dirname(__DIR__) . '/sdk/MainImport.php';

//Подключим его здесь, так как он ещё понадобится в админских приложениях
require_once 'actions/AbstractAjaxAction.php';

//Подключим путь для класслоадера
//Autoload::inst()->addBaseDir('ajax');
//Поставим признак ajax-запроса
PageContext::inst()->setAjaxContext();

function check_user_session_marker($marker) {
    if (!AuthManager::checkUserSessionMarker($marker)) {
        json_error('Передан некорректный маркер сессии');
    }
}

function json_error($error) {
    exit(json_encode(array('err' => $error)));
}

function json_success($data) {
    exit(json_encode(array('res' => $data)));
}

/**
 * Выполнение ajax действия
 * 
 * @param AjaxClassProvider $provider
 */
function execute_ajax_action(AbstractAjaxAction $action = null) {
    /* Для безопасности не будем писать детали обработки */
    if (!$action) {
        json_error('Действие не опеределено');
    }

    $result = $action->execute();
    $result = $result ? $result : 'Ошибка выполнения действия';

    if ($result instanceof AjaxSuccess) {
        json_success($result->getJsParams());
    }
    json_error($result);
}

?>