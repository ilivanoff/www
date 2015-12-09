$(function() {
    var maxlen = 50;
    FormHelper.registerOnce({
        form: '#MosaicAnswerForm',
        single: false,
        timeout: true,
        validator: {
            rules: {
                comment: {
                    required: true,
                    //maxlength: maxlen,
                    notex: true
                }
            },
            messages: {
                comment: {
                    required: 'Введите текст',
                    //maxlength: $.format('Максимальная длина поля: {0} символов', maxlen),
                    notex: 'В ответе не нужно указывать формулы'
                }
            }
        }
    });

});