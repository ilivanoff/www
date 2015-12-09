$(function() {
    
    FormHelper.registerOnce({
        form: '#RecoverGetCodeForm',
        onOk: 'На указанный Вами почтовый ящик отправлено письмо',
        single: false,
        validator: {
            rules: {
                r_mail: {
                    required: true,
                    maxlength: defs.EMAIL_MAXLEN,
                    email: true,
                    remote: {
                        url: "ajax/CheckEmail.php",
                        type: "post",
                        data: {
                            mp: 1
                        }
                    }
                }
            },
            messages: {
                r_mail: {
                    required: "Нужно указать e-mail",
                    maxlength: $.format('Максимальная длина поля: {0} символов', defs.EMAIL_MAXLEN),
                    email: "e-mail должен быть корректным",
                    remote : "Данный адрес не зарегистрирован"
                }
            }
        }
    });
});