$(function() {
    //Смена пароля
    FormHelper.registerOnce({
        form: '#PassChangeForm',
        onOk: 'Пароль успешно обновлён',
        validator: {
            rules: {
                r_old_pass: {
                    required: true,
                    remote: {
                        url: "ajax/CheckCurPass.php",
                        type: "post"
                    }
                },
                r_pass: {
                    required: true,
                    rangelength: [6, 80],
                    notEqualTo: "#PassChangeForm [name=r_old_pass]"
                },
                r_pass_conf: {
                    required: true,
                    equalTo: "#PassChangeForm [name=r_pass]"
                }
            },
            messages: {
                r_old_pass: {
                    required: "Нужно указать текущий пароль",
                    remote : "Текущий пароль указан неправильно"
                },
                r_pass: {
                    required: "Укажите новый пароль",
                    rangelength: "Пароль - от 6 до 80 символов",
                    notEqualTo: "Новый пароль совпадает со старым"
                },
                r_pass_conf: {
                    required: "Подтвердите новый пароль",
                    equalTo: "Подтверждение пароля не принято"
                }
            }
        }
    });
});    