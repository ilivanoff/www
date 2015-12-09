$(function() {
    FormHelper.registerOnce({
        form: '#RecoverUseCodeForm',
        onOk: 'Пароль успешно сменён',
        msgProgress: 'Обновляем пароль...',
        validator: {
            rules: {
                r_pass: {
                    required: true,
                    rangelength: [6, 80]
                
                },
                r_pass_conf: {
                    required: true,
                    equalTo: "#RecoverUseCodeForm [name=r_pass]"
                }
            },
            messages: {
                r_pass: {
                    required: "Введите пароль",
                    rangelength: "Пароль - от 6 до 80 символов"
                },
                r_pass_conf: {
                    required: "Подтвердите пароль",
                    equalTo: "Подтверждение пароля не принято"
                }
            }
        }
    });
});