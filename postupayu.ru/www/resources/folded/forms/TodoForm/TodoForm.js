$(function() {
    FormHelper.registerOnce({
        form: '#TodoForm',
        onOk: function() {
            return 'Действие выполнено';
        },
        validator: {
            rules: {
                text: {
                    required: true
                }
            },
            messages: {
                text: {
                    required: 'Введите текст'
                }
            }
        }
    });
});