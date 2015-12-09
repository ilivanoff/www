/*
 *  Регистрация
 */
$(function(){
    
    FormHelper.registerOnce({
        form: '#RegForm',
        onOk: 'Вы успешно зарегистрировались, поздравляем!',
        validator: {
            rules: {
                r_name: {
                    required: true,
                    maxlength: defs.SHORT_TEXT_MAXLEN,
                    notex: true
                },
                r_mail: {
                    required: true,
                    maxlength: defs.EMAIL_MAXLEN,
                    email: true,
                    remote: {
                        url: "ajax/CheckEmail.php",
                        type: "post"
                    }
                },
                r_sex: {
                    required: true
                },
                r_pass: {
                    required: true,
                    rangelength: [6, 80]
                },
                r_pass_conf: {
                    required: true,
                    equalTo: "#RegForm [name=r_pass]"
                }
            },
            messages: {
                r_name: {
                    required: "Укажите свое имя, пожалуйста",
                    maxlength: $.format('Максимальная длина поля: {0} символов', defs.SHORT_TEXT_MAXLEN),
                    notex: "Имя не может содержать формулы"
                },
                r_mail: {
                    required: "Нужно указать e-mail",
                    maxlength: $.format('Максимальная длина поля: {0} символов', defs.EMAIL_MAXLEN),
                    email: "e-mail должен быть корректным",
                    remote : "Данный адрес уже иcпользуется"
                },
                r_sex: {
                    required: "Укажите Ваш пол"
                },
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