$(function() {
    var $page = $('.APProfilers');
    
    var $controls = $page.find('.ctrl');
    var $buttons = $controls.children('button');

    $buttons.filter('.reset').button().uiButtonConfirm(function() {
        $buttons.uiButtonDisable();
        AdminAjaxExecutor.execute('ProfilersAction', {
            action: 'reset'
        } , function() {
            InfoBox.popupSuccess('Профайлеры успешно очищены');
            locationReload();
        }, function(err){
            InfoBox.popupError(err);
            $buttons.uiButtonEnable();
        });
    });

    $buttons.filter('.on').button({
        icons: {
            primary: 'ui-icon-play'
        }
    }).uiButtonConfirm(function() {
        $buttons.uiButtonDisable();
        AdminAjaxExecutor.execute('ProfilersAction', {
            action: 'on'
        } , function() {
            InfoBox.popupSuccess('Профилирование включено');
            locationReload();
        }, function(err){
            InfoBox.popupError(err);
            $buttons.uiButtonEnable();
        });
    });

    $buttons.filter('.off').button({
        icons: {
            primary: 'ui-icon-stop'
        }
    }).uiButtonConfirm(function() {
        $buttons.uiButtonDisable();
        AdminAjaxExecutor.execute('ProfilersAction', {
            action: 'off'
        } , function() {
            InfoBox.popupSuccess('Профилирование отключено');
            locationReload();
        }, function(err){
            InfoBox.popupError(err);
            $buttons.uiButtonEnable();
        });
    });

    $buttons.filter('.reload').button({
        text: false,
        icons: {
            primary: 'ui-icon-refresh'
        }
    }).click(function() {
        $buttons.uiButtonDisable();
        locationReload();
    });

});