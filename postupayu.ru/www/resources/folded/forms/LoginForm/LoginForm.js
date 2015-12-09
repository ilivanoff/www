$(function() {
    
    FormHelper.registerOnce({
        form: '#LoginForm',
        onOk: function() {
            PsLocalBus.fire(PsLocalBus.EVENT.LOGIN);
            return 'Выполняем вход...';
        },
        validator: {
            rules: {
                login: {
                    required: true,
                    email: true
                },
                password: {
                    required: true
                }
            },
            messages: {
                login: {
                    required: 'Укажите e-mail',
                    email: 'E-mail должен быть корректным'
                },
                password: {
                    required: 'Нужно указать пароль'
                }
            }
        }
    });

    $('a[href="#Login"]').live('click', function(event){
        event.preventDefault();
        PsUtil.callGlobalObject('RightPanelController', function() {
            RightPanelController.setVis(true);
        });
        $('#LoginForm [name=login]').focus().select();
    });
});