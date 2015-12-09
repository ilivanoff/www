$(function() {
    //Редактирование личных данных
    FormHelper.registerOnce({
        form: '#RegEditForm',
        onOk: 'Ваши данные успешно обновлены',
        validator: {
            rules: {
                r_name: {
                    required: true,
                    maxlength: defs.SHORT_TEXT_MAXLEN,
                    notex: true
                },
                r_sex: {
                    required: true
                }
            },
            messages: {
                r_name: {
                    required: 'Укажите свое имя, пожалуйста',
                    maxlength: $.format('Максимальная длина поля: {0} символов', defs.SHORT_TEXT_MAXLEN),
                    notex: 'Имя не может содержать формулы'
                },
                r_sex: {
                    required: 'Укажите Ваш пол'
                }
            }
        }
    });
});