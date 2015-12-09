$(function() {
    var $PANEL = $('#cb-adminoffice');
    if (MathJaxManager.isEnabled()){
        $PANEL.find('.MathJaxIndicator').html('Math Jax enabled').addClass('active');
    } else {
        $PANEL.find('.MathJaxIndicator').html('Math Jax disabled').addClass('inactive');
    }
        
    $PANEL.find('.SpeedTest a').confirmableClick({
        yes: function(){
            popupWindowManager.openWindow('speedtest', {
                url: getStringStart(window.location.href, '#', true)
            });
        }
    });

    $PANEL.find('.confirmable a').confirmableClick({
        yes: function($a){
            var $loading = span_progress('Выполняем');
            var reloadOnSuccess = $a.is('.reload');
            $a.replaceWith($loading);
                
            AdminClientManager.executeAjax({
                action: getHrefAnchor($a)
            },
            function(){
                $loading.replaceWith(span_success(reloadOnSuccess ? 'Перезагружаем...' : 'Выполнено'));
                if (reloadOnSuccess) {
                    locationReload(); 
                }
            }, $loading);
        }
    });
});