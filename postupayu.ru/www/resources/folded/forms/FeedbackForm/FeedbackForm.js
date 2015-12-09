/*
 *  Feedback
 *  
 *  Форма обратной связи
 */
$(function() {
    FormHelper.registerOnce({
        form: '#FeedbackForm',
        single: false,
        onOk: 'Спасибо за участие в развитии проекта, каждый отзыв очень важен для нас!',
        validator: {
            rules: {
                u_name: {
                    required: true,
                    maxlength: defs.SHORT_TEXT_MAXLEN,
                    notex: true
                },
                r_contacts: {
                    maxlength: defs.SHORT_TEXT_MAXLEN,
                    notex: true
                },
                theme: {
                    required: true,
                    maxlength: defs.SHORT_TEXT_MAXLEN,
                    notex: true
                },
                comment: {
                    required: true
                }
            },
            messages: {
                u_name: {
                    required:"Представьтесь, пожалуйста",
                    maxlength: $.format('Максимальная длина поля: {0} символов', defs.SHORT_TEXT_MAXLEN),
                    notex: "Имя не может содержать формулы"
                },
                r_contacts: {
                    maxlength: $.format('Максимальная длина поля: {0} символов', defs.SHORT_TEXT_MAXLEN),
                    notex: "Поле не может содержать формулы"
                },
                theme: {
                    required: "Укажите тему сообщения",
                    maxlength: $.format('Максимальная длина поля: {0} символов', defs.SHORT_TEXT_MAXLEN),
                    notex: "Тема не может содержать формулы"
                },
                comment: {
                    required: "Введите текст сообщения"
                }
            }
        }
    });

});